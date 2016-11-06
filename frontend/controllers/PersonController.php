<?php

namespace frontend\controllers;

use common\models\external\Reputation;
use common\models\PerformedAction;
use Yii;
use common\models\Person;
use common\models\PersonQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PersonController implements the CRUD actions for Person model.
 */
final class PersonController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'external-reputation'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Person::canUserIndexThem()) {
            Person::throwExceptionAboutIndex();
        }

        $searchModel = new PersonQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Person model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            Person::throwExceptionAboutView();
        }

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
     * @param $id
     * @return string|\yii\web\Response
     * @throws HttpException
     */
    public function actionExternalReputation($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            Person::throwExceptionAboutView();
        }

        $data = json_decode($model->data, true);

        if (isset($data['reputations'])) {
            $reputation = Reputation::createFromArray($data['reputations']);
            if ($reputation) {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('external/reputation', ['reputations' => $reputation]);
                } else {
                    return $this->render('external/reputation', ['reputations' => $reputation]);
                }
            }
        }

        throw new HttpException(404, Yii::t('external', 'NO_DATA'));
    }

    /**
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Person::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'PAGE_NOT_FOUND'));
        }
    }
}
