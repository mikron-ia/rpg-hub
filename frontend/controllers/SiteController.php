<?php
namespace frontend\controllers;

use common\models\Epic;
use common\models\EpicQuery;
use common\models\RecapQuery;
use common\models\StoryQuery;
use common\models\user\PasswordChange;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Cookie;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['captcha', 'error', 'login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'logout', 'password-change', 'set-epic'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
     * Displays front page
     * @return string
     */
    public function actionIndex()
    {
        /* Get Epic */
        if (!isset(Yii::$app->params['activeEpic'])) {
            $epic = null;
            $stories = null;
            $recap = null;
        } else {
            $epic = Yii::$app->params['activeEpic'];

            /* Get Recap */
            $recapQuery = new RecapQuery();
            $recap = $recapQuery->mostRecent();

            /* Get Stories */
            $searchModel = new StoryQuery();
            $stories = $searchModel->search(Yii::$app->request->queryParams);
        }

        /* Get Sessions */
        $sessions = [];

        /* Get News */
        $news = [];

        return $this->render('index', [
            'epic' => $epic,
            'sessions' => $sessions,
            'stories' => $stories,
            'news' => $news,
            'recap' => $recap,
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
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success',
                    'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Password change action
     *
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
     * Requests password reset.
     *
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
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
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
     * @return \yii\web\Response
     */
    public function actionSetEpic()
    {
        $chosenEpicKey = Yii::$app->request->post('epic');

        /* @var $chosenEpic Epic */
        $chosenEpic = EpicQuery::findOne(['key' => $chosenEpicKey]);
        Yii::$app->params['activeEpic'] = $chosenEpic;

        /* Save to cookie */
        $cookie = new Cookie([
            'name' => '_epic',
            'value' => $chosenEpic->key,
            'expire' => time() + 60 * 60 * 24 * 30, // 30 days
        ]);
        Yii::$app->response->cookies->add($cookie);

        $referrer = Yii::$app->getRequest()->getReferrer();

        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->goHome();
        }
    }
}
