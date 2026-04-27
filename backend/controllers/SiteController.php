<?php

namespace backend\controllers;

use common\models\Epic;
use common\models\EpicQuery;
use common\models\LoginForm;
use common\models\user\PasswordChange;
use common\models\user\UserAcceptForm;
use common\models\user\UserSettingsForm;
use Override;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

final class SiteController extends Controller
{
    #[Override]
    public function behaviors(): array
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
                            'settings',
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

    #[Override]
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionIndex(): Response|string
    {
        if (!isset(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-list');
        }

        return $this->redirect(['epic/front', 'key' => Yii::$app->params['activeEpic']->key]);
    }

    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
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

    public function actionMarkdownHelp(): string
    {
        return $this->render('markdownHelp');
    }

    public function actionPasswordChange(): Response|string
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

    public function actionLogout(): Response
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

    /**
     * @throws ForbiddenHttpException
     */
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
     */
    public function actionAccept(string $token): Response|string
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

    public function actionSettings(): Response|string
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
