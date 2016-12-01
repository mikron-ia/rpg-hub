<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\external\Reputation;
use common\models\external\ReputationEvent;
use Yii;
use common\models\Character;
use common\models\CharacterQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CharacterController implements the CRUD actions for Character model.
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
                        'actions' => ['index', 'view', 'external-reputation', 'external-reputation-event'],
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
     * Lists all Character models
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Character::canUserIndexThem()) {
            Character::throwExceptionAboutIndex();
        }

        $searchModel = new CharacterQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Character model
     * @param string $id
     * @return mixed
     * @throws HttpException
     */
    public function actionView($id)
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            throw new HttpException(412, strip_tags(Yii::t('app', 'ERROR_NO_EPIC_ACTIVE')));
        }

        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            Character::throwExceptionAboutView();
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

        throw new HttpException(404, Yii::t('external', 'NO_DATA'));
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws HttpException
     */
    public function actionExternalReputationEvent($id)
    {
        $model = $this->findModel($id);

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

        throw new HttpException(404, Yii::t('external', 'NO_DATA'));
    }

    /**
     * Finds the Character model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Character the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Character::findOne([
                'character_id' => $id,
                'visibility' => Visibility::determineVisibilityVector(),
            ])) !== null
        ) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }
    }
}
