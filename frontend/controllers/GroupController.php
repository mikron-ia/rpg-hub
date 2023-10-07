<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\Epic;
use common\models\Group;
use common\models\GroupQuery;
use frontend\controllers\external\ReputationToolsForControllerTrait;
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
    use ReputationToolsForControllerTrait;

    private const POSITIONS_PER_PAGE = 24;

    public function behaviors(): array
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
     *
     * @param null|string $key
     *
     * @return string
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionIndex(?string $key = null): string
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

        $searchModel = new GroupQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForUser(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Group model.
     *
     * @param string $key
     *
     * @return string
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
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
     * @param string $key
     *
     * @return string
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionExternalReputation(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
        }

        $reputation = $this->prepareReputationList($model);

        if ($reputation) {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('external/reputation', ['reputations' => $reputation]);
            } else {
                return $this->render('external/reputation', ['reputations' => $reputation]);
            }
        }

        throw new HttpException(204, Yii::t('external', 'NO_DATA'));
    }

    /**
     * @param string $key
     *
     * @return string
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionExternalReputationEvent(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
        }

        $event = $this->prepareReputationEventsList($model);
        if ($event) {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('external/reputation_event', ['events' => $event]);
            } else {
                return $this->render('external/reputation_event', ['events' => $event]);
            }
        }

        throw new HttpException(204, Yii::t('external', 'NO_DATA'));
    }

    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $key
     *
     * @return Group the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Group
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
