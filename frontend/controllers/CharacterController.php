<?php

namespace frontend\controllers;

use common\models\Character;
use common\models\CharacterQuery;
use common\models\core\Visibility;
use common\models\Epic;
use frontend\controllers\external\ReputationToolsForControllerTrait;
use common\components\EpicAssistance;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CharacterController implements the CRUD actions for Character model.
 */
final class CharacterController extends Controller
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
     * Lists all Character models
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
        } else {
            $epic = Yii::$app->params['activeEpic'];
        }

        if (!Character::canUserIndexThem()) {
            Character::throwExceptionAboutIndex();
        }

        $searchModel = new CharacterQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForUser(Yii::$app->request->queryParams);
        $groupTabs = CharacterQuery::getCharactersToShowInGroupTabAsDataObjects();
        $favoritesTab = CharacterQuery::getCharactersToShowInFavoritesTab();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'epic' => $epic,
            'tabsFromGroupData' => $groupTabs,
            'favorites' => $favoritesTab,
        ]);
    }

    /**
     * Displays a single Character model
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
    protected function findModelByKey(string $key): Character
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
     *
     * @return string|Response
     *
     * @throws HttpException
     */
    public function actionExternalReputation($key): Response|string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Character::throwExceptionAboutView();
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
            Character::throwExceptionAboutView();
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

    public function actionOpenScribbleModal(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Character::throwExceptionAboutView();
        }

        $scribbleModel = $model->scribblePack->getScribbleByUserId(Yii::$app->user->getId());

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('../scribble/_modal_box', ['model' => $scribbleModel]);
        } else {
            return $this->render('../scribble/_modal_box', ['model' => $scribbleModel]);
        }
    }

    /**
     * Finds the Character model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Character the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelById(string $id): Character
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
