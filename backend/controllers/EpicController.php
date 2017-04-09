<?php

namespace backend\controllers;

use common\models\Participant;
use Yii;
use common\models\Epic;
use common\models\EpicQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EpicController implements the CRUD actions for Epic model.
 */
final class EpicController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'create',
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
                'class' => VerbFilter::className(),
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
     * Displays a single Epic model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection', ['objectEpic' => $model]);
        }

        $model->canUserViewYou();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Epic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Epic::canUserCreateEpic();

        $model = new Epic();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->epic_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Epic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->canUserControlYou();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->epic_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Epic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

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
            return $this->redirect(['view', 'id' => $model->epic_id]);
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
            return $this->redirect(['view', 'id' => $model->epic_id]);
        } else {
            return $this->render('participant/edit', [
                'model' => $model,
            ]);
        }
    }

    public function actionManagerAttach($id)
    {
        $model = $this->findModel($id);

        if ($model->attachCurrentUserAsManager()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'MANAGE_EPIC_ATTACH_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'MANAGE_EPIC_ATTACH_FAILED'));
        }

        return $this->redirect(['manage']);
    }

    public function actionManagerDetach($id)
    {
        $model = $this->findModel($id);

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
     * @param string $id
     * @return Epic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Epic::findOne($id)) !== null) {
            if (empty(Yii::$app->params['activeEpic'])) {
                $this->run('site/set-epic-in-silence', ['epicKey' => $model->key]);
                Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_SET_BASED_ON_OBJECT'));
            } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
                $this->run('site/set-epic-in-silence', ['epicKey' => $model->key]);
                Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_CHANGED_BASED_ON_OBJECT'));
            }
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'PAGE_NOT_FOUND'));
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
            throw new NotFoundHttpException(Yii::t('app', 'PAGE_NOT_FOUND'));
        }
    }
}
