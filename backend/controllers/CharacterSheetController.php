<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Character;
use common\models\CharacterSheet;
use common\models\CharacterSheetQuery;
use common\models\core\CharacterSheetDataState;
use common\models\Epic;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CharacterSheetController implements the CRUD actions for CharacterSheet model
 */
final class CharacterSheetController extends Controller
{
    use EpicAssistance;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'create-character', 'index', 'load-data', 'update', 'view'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-character' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CharacterSheet models
     */
    public function actionIndex(?string $epic = null): string
    {
        if (!empty($epic)) {
            $epicObject = $this->findEpicByKey($epic);

            if (!$epicObject->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epicObject->key, $epicObject->epic_id, $epicObject->name);
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-list');
        }

        $searchModel = new CharacterSheetQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CharacterSheet model
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        $model->canUserViewYou();

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new CharacterSheet model
     */
    public function actionCreate(string $epic = null): Response|string
    {
        CharacterSheet::canUserCreateThem();

        $model = new CharacterSheet();

        $this->setEpicOnObject($epic, $model);
        $model->data_state = CharacterSheetDataState::Incomplete->value;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Creates a new character
     */
    public function actionCreateCharacter(string $key): Response
    {
        if (!Character::canUserCreateThem()) {
            Character::throwExceptionAboutCreate();
        }

        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            CharacterSheet::throwExceptionAboutView();
        }

        $character = Character::createForCharacterSheet($model);

        if ($character) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'CHARACTER_CREATE_FROM_CHARACTER_SHEET_SUCCESS'));
            if (!$model->currently_delivered_character_id) {
                $model->currently_delivered_character_id = $character->character_id;
                $model->save();
            }
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'CHARACTER_CREATE_FROM_CHARACTER_SHEET_FAILURE'));
        }

        return $this->redirect(['view', 'key' => $model->key]);
    }

    /**
     * Updates an existing CharacterSheet model
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModelByKey($key);

        $model->canUserControlYou();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Loads external data via paste box
     */
    public function actionLoadData(string $key): Response|string
    {
        $model = $this->findModelByKey($key);

        $model->canUserControlYou();

        if (!empty(Yii::$app->request->post())) {
            if ($model->loadExternal(Yii::$app->request->post('external-data', ''))) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'EXTERNAL_DATA_LOAD_SUCCESS'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'EXTERNAL_DATA_LOAD_FAILURE'));
            }
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('load_external', ['model' => $model]);
    }

    /**
     * Finds the CharacterSheet model based on its primary key value
     */
    protected function findModelByKey(string $key): CharacterSheet
    {
        $model = CharacterSheet::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_SHEET_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
