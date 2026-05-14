<?php

namespace frontend\controllers;

use common\models\CharacterSheet;
use common\models\CharacterSheetQuery;
use common\components\EpicAssistance;
use Override;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * CharacterSheetController implements the CRUD actions for CharacterSheet model.
 */
final class CharacterSheetController extends Controller
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
            ]
        ];
    }

    public function actionIndex(): string
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        $searchModel = new CharacterSheetQuery();
        $dataProvider = $searchModel->searchForFront(Yii::$app->request->queryParams);

        if($dataProvider->count === 1) {
            $models = $dataProvider->getModels();
            $this->redirect(['character-sheet/view', 'key' => array_pop($models)->key]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionView($key): string
    {
        $model = $this->findModel($key);

        if (!$model->canUserViewYou()) {
            CharacterSheet::throwExceptionAboutView();
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
    protected function findModel(string $key): CharacterSheet
    {
        if (($model = CharacterSheet::findOne(['key' => $key])) !== null) {
            $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_SHEET_NOT_AVAILABLE'));
    }
}
