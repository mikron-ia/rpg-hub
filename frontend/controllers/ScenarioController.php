<?php

namespace frontend\controllers;

use common\components\EpicAssistance;
use common\models\Epic;
use common\models\Scenario;
use common\models\ScenarioQuery;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ScenarioController extends Controller
{
    use EpicAssistance;

    private const POSITIONS_PER_PAGE = 24;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(?string $key = null): string
    {
        if (!empty($key)) {
            $epicObject = $this->findEpicByKey($key);

            if (!$epicObject->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epicObject->key, $epicObject->epic_id, $epicObject->name);
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        if (!Scenario::canUserIndexThem()) {
            Scenario::throwExceptionAboutIndex();
        }

        $searchModel = new ScenarioQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(string $key): string
    {
        $model = $this->findModel($key);

        if (!$model->canUserViewYou()) {
            Scenario::throwExceptionAboutView();
        }

        return $this->render('view', ['model' => $this->findModel($key)]);
    }

    protected function findModel(string $key): Scenario
    {
        $model = Scenario::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'SCENARIO_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
