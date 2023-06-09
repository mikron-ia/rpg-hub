<?php

namespace frontend\controllers;

use common\models\Character;
use common\models\CharacterQuery;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\external\Reputation;
use common\models\external\ReputationEvent;
use frontend\controllers\tools\EpicAssistance;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * CharacterController implements the CRUD actions for Character model.
 */
final class CharacterController extends Controller
{
    use EpicAssistance;

    private const POSITIONS_PER_PAGE = 24;

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
     * Lists all Character models
     * @param string|null $key
     * @return mixed
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

        if (!Character::canUserIndexThem()) {
            Character::throwExceptionAboutIndex();
        }

        $searchModel = new CharacterQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForUser(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Character model
     * @param string $key
     * @return mixed
     * @throws HttpException
     */
    public function actionView($key)
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Character::throwExceptionAboutView();
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Character the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey($key)
    {
        $model = Character::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }

        return $model;
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
            Character::throwExceptionAboutView();
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
            Character::throwExceptionAboutView();
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
     * Finds the Character model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Character the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelById($id)
    {
        $model = Character::findOne(['character_id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }

        return $model;
    }
}
