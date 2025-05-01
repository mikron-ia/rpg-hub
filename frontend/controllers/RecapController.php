<?php

namespace frontend\controllers;

use common\models\Epic;
use common\models\Recap;
use common\models\RecapQuery;
use common\components\EpicAssistance;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class RecapController extends Controller
{
    use EpicAssistance;

    private const POSITIONS_PER_PAGE = 4;

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Recap models
     *
     * @param string|null $key
     *
     * @return string
     *
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionIndex(?string $key = null)
    {
        if ($key) {
            $epic = $this->findEpicByKey($key);

            if (!$epic->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epic->key, $epic->epic_id, $epic->name);
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        if (!Recap::canUserIndexThem()) {
            Recap::throwExceptionAboutIndex();
        }

        $searchModel = new RecapQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->search([]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($key)
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Recap::throwExceptionAboutView();
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Story model based on its key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $key
     *
     * @return Recap the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Recap
    {
        $model = Recap::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'RECAP_NOT_AVAILABLE'));
        }

        return $model;
    }
}
