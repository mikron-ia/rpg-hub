<?php

namespace frontend\controllers;

use Yii;
use common\models\Story;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StoryController implements the CRUD actions for Story model.
 */
class StoryController extends Controller
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
     * Lists all Story models.
     * @return mixed
     */
    public function actionIndex()
    {
        Story::canUserIndexThem();

        $dataProvider = new ActiveDataProvider([
            'query' => Story::find()->orderBy('story_id DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Story model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $model->canUserViewYou();

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
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
