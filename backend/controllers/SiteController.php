<?php

namespace backend\controllers;

use common\models\Epic;
use common\models\EpicQuery;
use common\models\LoginForm;
use common\models\user\PasswordChange;
use common\models\user\UserAcceptForm;
use common\models\user\UserSettingsForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;
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
                        'actions' => ['about', 'login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'logout',
                            'markdown-help',
                            'password-change',
                            'set-epic',
                            'set-epic-in-silence',
                            'settings'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                    [
                        'actions' => ['accept'],
                        'allow' => true,
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
     * Displays the main page
     */
    public function actionIndex(): Response|string
    {
        if (!isset(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-list');
        }

        return $this->redirect(['epic/front', 'key' => Yii::$app->params['activeEpic']->key]);
    }

    /**
     * Displays login form or logs in the user
     * @return string|Response
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
     * Displays the markdown instructions
     */
    public function actionMarkdownHelp(): string
    {
        return $this->render('markdownHelp');
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
     * Logs out the user
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Selects Epic
     * @return Response
     */
    public function actionSetEpic(): Response
    {
        $chosenEpicKey = Yii::$app->request->post('epic');
        $this->run('site/set-epic-in-silence', ['epicKey' => $chosenEpicKey]);
        return $this->goHome();

    }

    public function actionSetEpicInSilence($epicKey): void
    {
        /* @var $chosenEpic Epic */
        $chosenEpic = EpicQuery::findOne(['key' => $epicKey]);

        if (!in_array($chosenEpic->epic_id, EpicQuery::allowedEpics(true))) {
            throw new ForbiddenHttpException(Yii::t('app', 'EPIC_NOT_ALLOWED_AUTOSELECT'));
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
     * Allows user to manipulate their settings
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
