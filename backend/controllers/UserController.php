<?php

namespace backend\controllers;

use backend\models\UserCreateForm;
use common\models\core\UserStatus;
use common\models\exceptions\InvalidBackendConfigurationException;
use common\models\UserInvitation;
use common\models\UserQuery;
use Throwable;
use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
final class UserController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'create',
                            'delete',
                            'disable',
                            'enable',
                            'index',
                            'invitations',
                            'renew',
                            'resend',
                            'revoke',
                            'update',
                            'view'
                        ],
                        'allow' => Yii::$app->user->can('controlUser'),
                        'roles' => ['manager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'disable' => ['post'],
                    'enable' => ['post'],
                    'renew' => ['post'],
                    'revoke' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all users
     */
    public function actionIndex(): string
    {
        $searchModel = new UserQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    /**
     * Lists all invitations
     */
    public function actionInvitations(): string
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
     * Displays a single user
     *
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Sends an invitation to a new user
     */
    public function actionCreate(): Response|string
    {
        $model = new UserCreateForm();

        if ($model->load(Yii::$app->request->post()) && $model->signUp()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'USER_CREATION_INVITE_SENT'));
                return $this->redirect(['invitations']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'USER_CREATION_INVITE_SENDING_FAILED'));
                return $this->redirect(['invitations']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing user
     *
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate(int $id): Response|string
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
     * Deletes the user
     *
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        if ($model->hasProtectedRole()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_DELETE_FAILED_PROTECTED'));
            return $this->redirect(['user/view', 'id' => $id]);
        }

        $model->setAttribute('status', UserStatus::Deleted->value);

        if ($model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'USER_DELETE_SUCCESS'));
            return $this->redirect(['user/index']);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_DELETE_FAILED'));
            return $this->redirect(['user/view', 'id' => $id]);
        }
    }

    /**
     * Disables the user
     *
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDisable(int $id): Response
    {
        $model = $this->findModel($id);

        if ($model->hasProtectedRole()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_DISABLE_FAILED_PROTECTED'));
            return $this->redirect(['user/view', 'id' => $id]);
        }

        $model->setAttribute('status', UserStatus::Disabled->value);

        if ($model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'USER_DISABLE_SUCCESS'));
            return $this->redirect(['user/index']);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_DISABLE_FAILED'));
            return $this->redirect(['user/view', 'id' => $id]);
        }
    }

    /**
     * Enables the user again
     *
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionEnable(int $id): Response
    {
        $model = $this->findModel($id);

        if (!$model->canBeEnabled()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_ENABLE_ERROR_MUST_BE_DISABLED'));
            return $this->redirect(['user/view', 'id' => $id]);
        }

        $model->setAttribute('status', UserStatus::Active->value);

        if ($model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'USER_ENABLE_SUCCESS'));
            return $this->redirect(['user/index']);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_ENABLE_FAILED'));
            return $this->redirect(['user/view', 'id' => $id]);
        }
    }

    /**
     * Revokes an invitation
     *
     * @throws Exception
     */
    public function actionRevoke(int $id): Response
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
     * Renews an invitation
     *
     * @throws Exception
     */
    public function actionRenew(int $id): Response
    {
        $model = UserInvitation::findOne($id);

        if ($model && $model->isRenewable()) {
            if ($model->renew()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'USER_INVITATION_RENEWED'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'USER_INVITATION_RENEWAL_FAILED'));
            }
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_INVITATION_RENEWAL_IMPOSSIBLE_USED'));
        }

        return $this->redirect(['invitations']);
    }

    /**
     * Re-sends an invitation
     *
     * @throws InvalidBackendConfigurationException
     */
    public function actionResend(int $id): Response
    {
        $model = UserInvitation::findOne($id);

        if ($model && $model->isInvitationValid()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'USER_INVITATION_SENT'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'USER_CREATION_INVITE_SENDING_FAILED'));
            }
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'USER_CREATION_INVITE_OVERDUE'));
        }

        return $this->redirect(['invitations']);
    }

    /**
     * Finds the User model based on its primary key value
     * If the model is not found, a 404 HTTP exception will be thrown
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'USER_NOT_AVAILABLE'));
        }
    }
}
