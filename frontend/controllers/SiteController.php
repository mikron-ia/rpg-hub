<?php

namespace frontend\controllers;

use common\models\AnnouncementQuery;
use common\models\Epic;
use common\models\EpicQuery;
use common\models\GameQuery;
use common\models\LoginForm;
use common\models\Participant;
use common\models\PerformedAction;
use common\models\RecapQuery;
use common\models\StoryQuery;
use common\models\User;
use common\models\user\PasswordChange;
use common\models\user\UserAcceptForm;
use common\models\user\UserSettingsForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Site controller
 */
final class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['captcha', 'error', 'login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'index',
                            'logout',
                            'password-change',
                            'set-epic',
                            'set-epic-in-silence',
                            'settings'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['about', 'accept'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['request-password-reset', 'reset-password'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays the about page
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    /**
     * Displays front page
     *
     * @return string
     *
     * @throws HttpException
     */
    public function actionIndex(): string
    {
        $user = User::findOne(['id' => \Yii::$app->user->id]);

        $userEpicIDs = array_map(function (Participant $participation) {
            return $participation->epic_id;
        }, $user->getParticipants()->all());

        $epics = EpicQuery::activeEpicsAsModels(false);
        $sessions = (new GameQuery())->mostRecentByPlayerDataProvider($userEpicIDs);
        $recaps = (new RecapQuery())->mostRecentByPlayerDataProvider($userEpicIDs);
        $stories = (new StoryQuery(4))->mostRecentByPlayerDataProvider($userEpicIDs);
        $announcements = (new AnnouncementQuery())->mostRecentByPlayerDataProvider($userEpicIDs);

        // @todo Recap sighting

        return $this->render('index', [
            'epics' => $epics,
            'sessions' => $sessions,
            'stories' => $stories,
            'announcements' => $announcements,
            'recaps' => $recaps,
        ]);
    }

    /**
     * Logs in a user
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user
     * @return mixed
     */
    public function actionLogout()
    {
        PerformedAction::createSimplifiedRecord(PerformedAction::PERFORMED_ACTION_LOGOUT);

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Password change action
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionPasswordChange()
    {
        $model = new PasswordChange();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->savePassword()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'PASSWORD_CHANGE_FLASH_SUCCESS'));
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'PASSWORD_CHANGE_FLASH_FAILURE'));
            }
        }

        return $this->render('user/password-change', ['model' => $model]);
    }

    /**
     * Requests password reset
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Selects Epic
     * @return Response
     */
    public function actionSetEpic(): Response
    {
        $chosenEpicKey = Yii::$app->request->post('epic');
        $this->run('site/set-epic-in-silence', ['epicKey' => $chosenEpicKey]);
        return $this->redirect(Yii::$app->urlManager->createUrl(['epic/view', 'key' => $chosenEpicKey]));
    }

    public function actionSetEpicInSilence($epicKey)
    {
        /* @var $chosenEpic Epic */
        $chosenEpic = EpicQuery::findOne(['key' => $epicKey]);

        if (!in_array($chosenEpic->epic_id, EpicQuery::allowedEpics(false))) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'EPIC_NOT_ALLOWED'));
        } else {
            if ($chosenEpic) {
                Yii::$app->params['activeEpic'] = $chosenEpic;

                /* Save to cookie */
                $cookie = new Cookie([
                    'name' => '_epic',
                    'value' => $chosenEpic->key,
                    'expire' => time() + 60 * 60 * 24 * 8,
                ]);
                Yii::$app->response->cookies->add($cookie);
            }
        }
    }

    /**
     * Creates a new User, based on an invitation
     * @param string $token
     * @return mixed
     */
    public function actionAccept($token)
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('success', Yii::t('app', 'USER_CREATION_CURRENT_USER_LOGGED_OUT'));
        }

        try {
            $model = new UserAcceptForm($token);
        } catch (InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error',
                Yii::t('app', 'USER_CREATION_FAILED_WRONG_TOKEN {reason}', ['reason' => $e->getMessage()]));
            return $this->redirect(['site/index']);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error',
                Yii::t('app', 'USER_CREATION_FAILED_OTHER {reason}', ['reason' => $e->getMessage()]));
            return $this->redirect(['site/index']);
        }

        Yii::$app->language = $model->language;

        if ($model->load(Yii::$app->request->post()) && $model->signUp()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'USER_CREATION_COMPLETED'));
            return $this->redirect(['site/index']);
        } else {
            return $this->render('user/accept', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function actionSettings()
    {
        $model = new UserSettingsForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'USER_SETTINGS_CHANGED'));
            return $this->redirect(['index']);
        }

        return $this->render('user/settings', [
            'model' => $model,
        ]);
    }
}
