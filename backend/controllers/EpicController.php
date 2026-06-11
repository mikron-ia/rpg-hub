<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\AnnouncementQuery;
use common\models\Epic;
use common\models\EpicQuery;
use common\models\GameQuery;
use common\models\Parameter;
use common\models\Participant;
use common\models\ParticipantRole;
use common\models\ProjectQuery;
use common\models\RecapQuery;
use common\models\state\EpicStatus;
use common\models\Story;
use common\models\StoryQuery;
use Override;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class EpicController extends CmsController
{
    use EpicAssistance;

    private const int POSITIONS_PER_PAGE = 8;

    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'create-as-gm',
                            'front',
                            'index',
                            'update',
                            'view',
                            'participant-add',
                            'participant-edit',
                            'participant-delete',
                            'set-current-story',
                            'switch-state',
                            'create-parameter',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                    [
                        'actions' => [
                            'create-as-manager',
                            'manage',
                            'manager-attach',
                            'manager-detach',
                        ],
                        'allow' => true,
                        'roles' => ['manager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'participant-delete' => ['DELETE'],
                    'manager-attach' => ['POST'],
                    'manager-detach' => ['POST'],
                    'set-current-story' => ['PATCH'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): string
    {
        Epic::canUserIndexEpic();

        $searchModel = new EpicQuery();
        $dataProvider = $searchModel->searchForCmsIndex(
            params: Yii::$app->request->queryParams,
            positionsPerPage: self::POSITIONS_PER_PAGE,
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'showManagerButton' => Yii::$app->user->can('manageEpic'),
        ]);
    }

    /**
     * Lists all Epic models for management purposes
     *
     * @throws HttpException
     */
    public function actionManage(): string
    {
        Epic::canUserManageEpic();

        $searchModel = new EpicQuery();
        $dataProvider = $searchModel->manageableEpicsAsActiveDataProvider();

        return $this->render('manage', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'showManagerButton' => true, // if you got to this point without a 403, you must have the right to do that
        ]);
    }

    /**
     * Displays the Epic's main page
     *
     * @throws Exception
     * @throws HttpException
     */
    public function actionFront(string $key): string
    {
        $model = $this->findModel($key);
        $model->canUserViewYou();

        $this->selectEpic($model->key, $model->epic_id, $model->name, false);

        $model->recordSighting();

        /* Get Recap */
        $recapQuery = new RecapQuery();
        $recap = $recapQuery->mostRecentForEpic($model);

        $recap?->recordSighting();

        /* Get Stories */
        $searchModel = new StoryQuery(4);
        $stories = $searchModel->searchForEpic(Yii::$app->request->queryParams, $model);

        /* Get Projects */
        $searchModel = new ProjectQuery(4);
        $projects = $searchModel->searchForEpic(Yii::$app->request->queryParams, $model);

        /* Get Sessions */
        $sessionQuery = new GameQuery();
        $sessions = $sessionQuery->mostRecentDataProvider($model);

        /* Get Announcements */
        $announcementQuery = new AnnouncementQuery();
        $announcements = $announcementQuery->mostRecentDataProvider($model, false);

        return $this->render('front', [
            'epic' => $model,
            'announcements' => $announcements,
            'sessions' => $sessions,
            'stories' => $stories,
            'projects' => $projects,
            'recap' => $recap,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModel($key);
        $model->canUserViewYou();
        $this->selectEpic($model->key, $model->epic_id, $model->name);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreateAsGm(): Response|string
    {
        Epic::canUserCreateEpic();

        $model = new Epic();

        if ($model->load(Yii::$app->request->post()) && $model->save() && $model->refresh()) {
            if (!Participant::createForEpic($model->epic_id, Yii::$app->user->id, ParticipantRole::ROLE_GM)) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_GM_ADDED'));
            }
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('create-as-gm', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreateAsManager(): Response|string
    {
        Epic::canUserCreateEpic();

        $model = new Epic();

        if ($model->load(Yii::$app->request->post()) && $model->save() && $model->refresh()) {
            if (!Participant::createForEpic($model->epic_id, Yii::$app->user->id, ParticipantRole::ROLE_MANAGER)) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_MANAGER_ADDED'));
            }
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('create-as-manager', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModel($key);

        $model->canUserControlYou();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(string $key): Response
    {
        $model = $this->findModel($key);

        $model->canUserControlYou();

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionParticipantAdd(string $key): Response|string
    {
        $epic = $this->findModel($key);

        $participant = new Participant();

        $this->selectEpic($epic->key, $epic->epic_id, $epic->name, false);

        $participant->epic_id = $epic->epic_id;

        if ($participant->load(Yii::$app->request->post()) && $participant->save()) {
            return $this->redirect(['view', 'key' => $participant->epic->key]);
        }

        return $this->render('participant/add', ['model' => $participant]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionParticipantEdit(string $key): Response|string
    {
        $participant = $this->findParticipantModel($key);

        $this->selectEpic($participant->epic->key, $participant->epic_id, $participant->epic->name, false);

        if ($participant->load(Yii::$app->request->post()) && $participant->save()) {
            return $this->redirect(['view', 'key' => $participant->epic->key]);
        }

        return $this->render('participant/edit', [
            'model' => $participant,
        ]);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionParticipantDelete(string $key): Response
    {
        $participant = $this->findParticipantModel($key);

        if (!empty($participant->participantRoles)) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_PARTICIPANT_REMOVAL_HAS_ROLES'));
            return $this->redirect(['view', 'key' => Yii::$app->params['activeEpic']->key]);
        }

        $this->selectEpic($participant->epic->key, $participant->epic_id, $participant->epic->name, false);

        if ($participant->delete() === false) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_PARTICIPANT_DELETE_FAILURE'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('app', 'ERROR_PARTICIPANT_DELETE_SUCCESS'));
        }

        return $this->redirect(['view', 'key' => Yii::$app->params['activeEpic']->key]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionManagerAttach(string $key): Response
    {
        $model = $this->findModel($key);

        if ($model->attachCurrentUserAsManager()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'MANAGE_EPIC_ATTACH_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'MANAGE_EPIC_ATTACH_FAILED'));
        }

        return $this->redirect(['manage']);
    }

    /**
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionManagerDetach(string $key): Response
    {
        $model = $this->findModel($key);

        if ($model->detachCurrentUserAsManager()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'MANAGE_EPIC_DETACH_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'MANAGE_EPIC_DETACH_FAILED'));
        }

        return $this->redirect(['manage']);
    }

    /**
     * @throws MethodNotAllowedHttpException
     */
    public function actionSetCurrentStory(string $epicKey, string $storyKey): Response
    {
        if (!Yii::$app->request->isPatch) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_PATCH_REQUESTS_ONLY'));
        }

        try {
            $epic = $this->findModel($epicKey);
            $epic->canUserControlYou();
        } catch (HttpException) {
            // @todo Add logging
            Yii::$app->session->setFlash('error', Yii::t('app', 'EPIC_NOT_AVAILABLE'));
            return $this->redirect(['story/view', 'key' => $storyKey]);
        }

        try {
            $story = $this->findStoryModel($storyKey);
        } catch (HttpException) {
            // @todo Add logging
            Yii::$app->session->setFlash('error', Yii::t('app', 'STORY_NOT_AVAILABLE'));
            return $this->redirect(['story/index']);
        }

        if ((int)$epic->epic_id !== $story->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'STORY_WRONG_EPIC_ERROR'));
            return $this->redirect(['story/view', 'key' => $storyKey]);
        }

        $epic->current_story_id = $story->story_id;

        $saveSuccessful = false;
        try {
            $saveSuccessful = $epic->save(false);
        } catch (Exception) {
            // @todo Add logging
        }

        Yii::$app->session->setFlash(
            $saveSuccessful ? 'success' : 'error',
            $saveSuccessful ? Yii::t('app', 'EPIC_STORY_SET_SUCCESS') : Yii::t('app', 'EPIC_STORY_SET_FAILED')
        );

        return $this->redirect(['story/view', 'key' => $story->key]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionSwitchState(string $key, string $command): Response
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Epic::throwExceptionAboutControl();
        }

        $newStatus = EpicStatus::tryFrom($command);
        if ($newStatus === null || !in_array($newStatus, $model->getStatus()->getAllowedSuccessors())) {
            Yii::$app->session->setFlash(
                'error',
                Yii::t('app', 'EPIC_STATUS_CHANGE_ERROR_INVALID_STATUS')
            );
        } else {
            $model->status = $newStatus->value;
            $model->save();
            Yii::$app->session->setFlash(
                'success',
                Yii::t(
                    'app', 'EPIC_STATUS_CHANGE_SUCCESS {target}',
                    ['target' => strtolower($newStatus->getName())]
                )
            );
        }

        return $this->returnToReferrer(['view', 'key' => $key]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreateParameter(string $key): Response|string
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Epic::throwExceptionAboutControl();
        }

        $parameter = new Parameter();
        $loadSuccess = $parameter->load(Yii::$app->request->post());
        $parameter->parameter_pack_id = $model->parameterPack->parameter_pack_id;

        if ($loadSuccess && $parameter->save()) {
            return $this->returnToReferrer(['site/index']);
        }

        $dataForCreate = [
            'model' => $parameter,
            'creatorController' => 'epic',
            'creatorKey' => $model->key,
        ];

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('../parameter/create', $dataForCreate);
        }

        return $this->render('../parameter/create', $dataForCreate);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(string $key): Epic
    {
        if (($model = Epic::findOne(['key' => $key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'EPIC_NOT_FOUND'));
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findParticipantModel(string $key): Participant
    {
        if (($model = Participant::findOne(['key' => $key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'PARTICIPANT_NOT_FOUND'));
    }

    /**
     * @throws HttpException
     */
    private function findStoryModel(string $key): Story
    {
        if (($model = Story::findOne(['key' => $key])) !== null) {
            if (!$model->canUserViewYou()) {
                Story::throwExceptionAboutView();
            }
            return $model;
        }

        throw new NotFoundHttpException();
    }
}
