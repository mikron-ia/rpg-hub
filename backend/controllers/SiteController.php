<?php
namespace backend\controllers;

use common\models\Epic;
use common\models\EpicQuery;
use common\models\LoginForm;
use common\models\user\PasswordChange;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Cookie;

/**
 * Site controller
 */
final class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'password-change', 'set-epic'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['operator'],
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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (!isset(Yii::$app->params['activeEpic'])) {
            return $this->render('epic-selection');
        }

        $epic = Yii::$app->params['activeEpic'];

        return $this->render('index', [
            'epic' => $epic,
        ]);
    }

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

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
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
