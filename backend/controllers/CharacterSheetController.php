<?php

namespace backend\controllers;

use common\models\Character;
use Yii;
use common\models\CharacterSheet;
use common\models\CharacterSheetQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CharacterSheetController implements the CRUD actions for CharacterSheet model.
 */
final class CharacterSheetController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'create-character', 'index', 'update', 'view'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create-character' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CharacterSheet models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        $searchModel = new CharacterSheetQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CharacterSheet model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection', ['objectEpic' => $model->epic]);
        }

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
     * Creates a new CharacterSheet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        CharacterSheet::canUserCreateThem();

        $model = new CharacterSheet();

        $model->setCurrentEpicOnEmpty();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->character_sheet_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new character
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return mixed
     */
    public function actionCreateCharacter($id)
    {
        if (!Character::canUserCreateThem()) {
            Character::throwExceptionAboutCreate();
        }

        $model = $this->findModel($id);

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

        return $this->redirect(['view', 'id' => $model->character_sheet_id]);
    }

    /**
     * Updates an existing CharacterSheet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public
    function actionUpdate(
        $id
    ) {
        $model = $this->findModel($id);

        $model->canUserControlYou();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->character_sheet_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the CharacterSheet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CharacterSheet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel(
        $id
    ) {
        if (($model = CharacterSheet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'PAGE_NOT_FOUND'));
        }
    }
}
