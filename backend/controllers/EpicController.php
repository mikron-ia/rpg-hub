<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\AnnouncementQuery;
use common\models\Epic;
use common\models\EpicQuery;
use common\models\GameQuery;
use common\models\Participant;
use common\models\ParticipantRole;
use common\models\RecapQuery;
use common\models\Story;
use common\models\StoryQuery;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class EpicController extends Controller
{
    use EpicAssistance;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'create',
                            'front',
                            'index',
                            'update',
                            'view',
                            'participant-add',
                            'participant-edit',
                            'participant-delete',
                            'set-current-story',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                    [
                        'actions' => [
                            'manage',
                            'manager-attach',
                            'manager-detach'
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
        $dataProvider = $searchModel->activeEpicsAsActiveDataProvider();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Epic models for management purposes
     *
     * @throws HttpException
     */
    public function actionManage(): string
    {
        Epic::canUserIndexEpic();

        $searchModel = new EpicQuery();
        $dataProvider = $searchModel->manageableEpicsAsActiveDataProvider();

        return $this->render('manage', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
     * @throws HttpException
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        Epic::canUserCreateEpic();

        $model = new Epic();

        if ($model->load(Yii::$app->request->post()) && $model->save() && $model->refresh()) {
            if (!Participant::createForEpic($model->epic_id, Yii::$app->user->id, ParticipantRole::ROLE_GM)) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_GM_ADDED'));
            }
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     * @throws Exception
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
        $this->findModel($key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionParticipantAdd(string $key): Response|string
    {
        $model = new Participant();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->key <> $key) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC_ACTION'));
        }

        $model->epic_id = $this->findModel($key)->epic_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->epic->key]);
        }

        return $this->render('participant/add', ['model' => $model]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionParticipantEdit(string $participant_id): Response|string
    {
        $model = $this->findParticipantModel($participant_id);

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->epic->key]);
        }

        return $this->render('participant/edit', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionParticipantDelete(string $participant_id): Response
    {
        $model = $this->findParticipantModel($participant_id);

        if (!empty($model->participantRoles)) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_PARTICIPANT_REMOVAL_HAS_ROLES'));
            return $this->redirect(['view', 'key' => Yii::$app->params['activeEpic']->key]);
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            return $this->redirect(['view', 'key' => Yii::$app->params['activeEpic']->key]);
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
            return $this->redirect(['view', 'key' => Yii::$app->params['activeEpic']->key]);
        }

        if ($model->delete() === false) {
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
            $model = $this->findModel($epicKey);
            $model->canUserControlYou();
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

        $model->current_story_id = $story->story_id;

        $saveSuccessful = false;
        try {
            $saveSuccessful = $model->save(false);
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
    protected function findParticipantModel(string $id): Participant
    {
        if (($model = Participant::findOne($id)) !== null) {
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
