<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\Epic;
use common\models\Location;
use common\models\LocationQuery;
use frontend\controllers\external\ReputationToolsForControllerTrait;
use common\components\EpicAssistance;
use Override;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class LocationController extends Controller
{
    use EpicAssistance;
    use ReputationToolsForControllerTrait;

    private const int POSITIONS_PER_PAGE = 24;

    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'view',
                            'open-scribble-modal',
                        ],
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
     * @throws HttpException
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

        $epic = Yii::$app->params['activeEpic'];

        if (!Location::canUserIndexThem()) {
            Location::throwExceptionAboutIndex();
        }

        $searchModel = new LocationQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForUser(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'epic' => $epic,
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Location::throwExceptionAboutView();
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
            'showPrivates' => $model->canUserControlYou(),
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionOpenScribbleModal(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Location::throwExceptionAboutView();
        }

        $scribbleModel = $model->scribblePack->getScribbleByUserId(Yii::$app->user->getId());

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('../scribble/_modal_box', ['model' => $scribbleModel]);
        }

        return $this->render('../scribble/_modal_box', ['model' => $scribbleModel]);
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Location
    {
        $model = Location::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'LOCATION_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'LOCATION_NOT_AVAILABLE'));
        }

        return $model;
    }
}
