<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\Epic;
use common\models\Project;
use common\models\ProjectQuery;
use common\components\EpicAssistance;
use Override;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

final class ProjectController extends Controller
{
    use EpicAssistance;

    private const int POSITIONS_PER_PAGE = 4;

    #[Override]
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

        if (!Project::canUserIndexThem()) {
            Project::throwExceptionAboutIndex();
        }

        $searchModel = new ProjectQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->search([]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
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
            Project::throwExceptionAboutView();
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $model->recordSighting();

        $canSeePrivates = $model->canUserControlYou();

        return $this->render('view', [
            'model' => $model,
            'showPrivates' => $canSeePrivates,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModelByKey(string $key): Project
    {
        $model = Project::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'PROJECT_NOT_AVAILABLE'));
        }

        if (!in_array($model->getVisibility(), Visibility::determineVisibilityVectorWithObjects($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'PROJECT_NOT_AVAILABLE'));
        }

        return $model;
    }
}
