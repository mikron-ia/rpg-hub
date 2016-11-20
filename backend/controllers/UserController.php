<?php

namespace backend\controllers;

use backend\models\UserCreateForm;
use common\models\UserInvitation;
use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
final class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'delete', 'index', 'invitations', 'revoke', 'update', 'view'],
                        'allow' => Yii::$app->user->can('controlUser'),
                        'roles' => ['manager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'revoke' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all invitations
     * @return mixed
     */
    public function actionInvitations()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserInvitation::find(),
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        return $this->render('invitation/index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserCreateForm();

        if ($model->load(Yii::$app->request->post()) && $model->signUp()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'USER_CREATION_INVITE_SENT'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'USER_CREATION_INVITE_SENDING_FAILED'));
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionRevoke($id)
    {
        $model = UserInvitation::findOne($id);

        if ($model && $model->isInvitationUnRevoked() && $model->markAsRevoked()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'USER_INVITATION_REVOKED'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_INVITATION_REVOKE_FAILED'));
        }

        return $this->redirect(['invitations']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
