<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\Epic;
use common\models\Group;
use common\models\GroupQuery;
use common\models\StoryGroupAssignmentQuery;
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

class GroupController extends Controller
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
                            'external-reputation',
                            'external-reputation-event',
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

        if (!Group::canUserIndexThem()) {
            Group::throwExceptionAboutIndex();
        }

        $searchModel = new GroupQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForUser(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'epic' => $epic,
        ]);
    }

    /**
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

        if ($model->canUserControlYou()) { // not the best, but only real possible way of verifying user access level
            $storyGroupPublic = StoryGroupAssignmentQuery::getStoryAssignmentPublicLinksForOperator($model->group_id);
            $storyGroupPrivate = StoryGroupAssignmentQuery::getStoryAssignmentPrivateLinksForOperator($model->group_id);
        } else {
            $storyGroupPublic = StoryGroupAssignmentQuery::getStoryAssignmentLinksForUser($model->group_id);
            $storyGroupPrivate = [];
        }

        return $this->render('view', [
            'model' => $model,
            'storyGroupPublic' => $storyGroupPublic,
            'storyGroupPrivate' => $storyGroupPrivate,
            'showPrivates' => $model->canUserControlYou(),
        ]);
    }

    /**
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
            }

            return $this->render('external/reputation', ['reputations' => $reputation]);
        }

        throw new HttpException(204, Yii::t('external', 'NO_DATA'));
    }

    /**
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
            }

            return $this->render('external/reputation_event', ['events' => $event]);
        }

        throw new HttpException(204, Yii::t('external', 'NO_DATA'));
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionOpenScribbleModal(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
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
