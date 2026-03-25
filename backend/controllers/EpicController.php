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
use common\models\StoryQuery;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
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
                    'manager-attach' => ['POST'],
                    'manager-detach' => ['POST'],
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
}
