<?php

namespace frontend\controllers;

use common\models\Article;
use common\models\ArticleQuery;
use common\models\core\Visibility;
use common\models\Epic;
use common\components\EpicAssistance;
use Override;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
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
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
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

        if (!Article::canUserIndexThem()) {
            Article::throwExceptionAboutIndex();
        }

        $searchModel = new ArticleQuery();
        $dataProvider = $searchModel->searchForUser();

        return $this->render('index', [
            'searchModel' => $searchModel,
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
            Article::throwExceptionAboutView();
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModelByKey(string $key): Article
    {
        $model = Article::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'ARTICLE_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineUnsafeVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'ARTICLE_NOT_AVAILABLE'));
        }

        return $model;
    }
}
