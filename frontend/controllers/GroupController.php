<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\Epic;
use common\models\external\Reputation;
use common\models\external\ReputationEvent;
use common\models\Group;
use common\models\GroupQuery;
use frontend\controllers\tools\EpicAssistance;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * GroupController implements the CRUD actions for Group model.
 */
class GroupController extends Controller
{
    use EpicAssistance;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'external-reputation', 'external-reputation-event'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }

    /**
     * Lists all Group models.
     * @param null|string $key
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
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

        if (!Group::canUserIndexThem()) {
            Group::throwExceptionAboutIndex();
        }

        $searchModel = new GroupQuery(24);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Group model.
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
     */
    public function actionView($key)
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $key
     * @return string|\yii\web\Response
     * @throws HttpException
     */
    public function actionExternalReputation($key)
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
        }

        if ($model->external_data_pack_id) {
            $data = $model->externalDataPack->getExternalDataByCode('reputations');
            if (isset($data)) {
                $reputation = Reputation::createFromArray($data);
                if ($reputation) {
                    if (Yii::$app->request->isAjax) {
                        return $this->renderAjax('external/reputation', ['reputations' => $reputation]);
                    } else {
                        return $this->render('external/reputation', ['reputations' => $reputation]);
                    }
                }
            }
        }

        throw new HttpException(204, Yii::t('external', 'NO_DATA'));
    }

    /**
     * @param $key
     * @return string|\yii\web\Response
     * @throws HttpException
     */
    public function actionExternalReputationEvent($key)
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
        }

        if ($model->external_data_pack_id) {
            $data = $model->externalDataPack->getExternalDataByCode('reputationEvents');
            if (isset($data)) {
                $event = ReputationEvent::createFromArray($data);
                if ($event) {
                    if (Yii::$app->request->isAjax) {
                        return $this->renderAjax('external/reputation_event', ['events' => $event]);
                    } else {
                        return $this->render('external/reputation_event', ['events' => $event]);
                    }
                }
            }
        }

        throw new HttpException(204, Yii::t('external', 'NO_DATA'));
    }

    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Group the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey($key)
    {
        $model = Group::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'GROUP_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'GROUP_NOT_AVAILABLE'));
        }

        return $model;
    }
}
