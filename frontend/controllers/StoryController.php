<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\StoryQuery;
use Yii;
use common\models\Story;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * StoryController implements the CRUD actions for Story model.
 */
final class StoryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
     * Lists all Story models
     * @return mixed
     */
    public function actionIndex()
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        if (!Story::canUserIndexThem()) {
            Story::throwExceptionAboutIndex();
        }

        $searchModel = new StoryQuery(4);
        $dataProvider = $searchModel->search([]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Story model
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            Story::throwExceptionAboutView();
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $model->epic->key]);
            Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_SET_BASED_ON_OBJECT'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $model->epic->key]);
            Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_CHANGED_BASED_ON_OBJECT'));
        }

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Story::findOne(['story_id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        return $model;
    }
}
