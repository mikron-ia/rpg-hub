<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use common\models\Epic;
use common\models\EpicQuery;
use common\models\GameQuery;
use common\models\Participant;
use common\models\ParticipantRole;
use common\models\RecapQuery;
use common\models\StoryQuery;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * EpicController implements the CRUD actions for Epic model.
 */
final class EpicController extends Controller
{
    use EpicAssistance;

    public function behaviors()
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
     * Lists all Epic models user can access
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionIndex()
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
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionManage()
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

        /* Get News */
        $news = [];

        return $this->render('front', [
            'epic' => $model,
            'sessions' => $sessions,
            'stories' => $stories,
            'news' => $news,
            'recap' => $recap,
        ]);
    }

    /**
     * Displays a single Epic model.
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
     */
    public function actionView($key)
    {
        $model = $this->findModel($key);
        $model->canUserViewYou();
        $this->selectEpic($model->key, $model->epic_id, $model->name);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Epic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionCreate()
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
     * Updates an existing Epic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($key)
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
     * Deletes an existing Epic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($key)
    {
        $this->findModel($key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Adds a participant
     * @param string $epic_id
     * @return mixed
     */
    public function actionParticipantAdd($epic_id)
    {
        $model = new Participant();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC_ACTION'));
        }

        $model->epic_id = $epic_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->epic->key]);
        } else {
            return $this->render('participant/add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Edits a participant
     * @param string $participant_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionParticipantEdit($participant_id)
    {
        $model = $this->findParticipantModel($participant_id);

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->epic->key]);
        } else {
            return $this->render('participant/edit', [
                'model' => $model,
            ]);
        }
    }

    public function actionManagerAttach($key)
    {
        $model = $this->findModel($key);

        if ($model->attachCurrentUserAsManager()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'MANAGE_EPIC_ATTACH_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'MANAGE_EPIC_ATTACH_FAILED'));
        }

        return $this->redirect(['manage']);
    }

    public function actionManagerDetach($key)
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
     * Finds the Epic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Epic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($key)
    {
        if (($model = Epic::findOne(['key' => $key])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'EPIC_NOT_FOUND'));
        }
    }

    /**
     * Finds the UserEpic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Participant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findParticipantModel($id)
    {
        if (($model = Participant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'PARTICIPANT_NOT_FOUND'));
        }
    }
}
