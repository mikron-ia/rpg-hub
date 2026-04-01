<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Epic;
use common\models\PointInTime;
use common\models\PointInTimeQuery;
use Override;
use Throwable;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception as DbException;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PointInTimeController extends Controller
{
    use EpicAssistance;

    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'view', 'delete', 'move-up', 'move-down'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionIndex(string $epic): string
    {
        if (!empty($epic)) {
            $epicObject = $this->findEpicByKey($epic);

            if (!$epicObject->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epicObject->key, $epicObject->epic_id, $epicObject->name);
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-list');
        }

        if (!PointInTime::canUserIndexThem()) {
            PointInTime::throwExceptionAboutIndex();
        }

        $searchModel = new PointInTimeQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            PointInTime::throwExceptionAboutView();
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * @throws DbException
     * @throws HttpException
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!PointInTime::canUserCreateThem()) {
            PointInTime::throwExceptionAboutCreate();
        }

        $model = new PointInTime();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_in_time_id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * @throws DbException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_in_time_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index', 'epic' => $model->epic->key]);
    }

    /**
     * Moves PointInTime up in order; this means a lower position on the list
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws InvalidRouteException
     */
    public function actionMoveUp(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }
        $model->movePrev();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index', 'epic' => $model->epic->key]);
    }

    /**
     * Moves PointInTime down in order; this means a higher position on the list
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws InvalidRouteException
     */
    public function actionMoveDown(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }
        $model->moveNext();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index', 'epic' => $model->epic->key]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): PointInTime
    {
        $model = PointInTime::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'POINT_IN_TIME_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
