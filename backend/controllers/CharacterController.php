<?php

namespace backend\controllers;

use common\models\CharacterSheet;
use common\models\EpicQuery;
use common\models\Parameter;
use common\models\tools\Retriever;
use Yii;
use common\models\Character;
use common\models\CharacterQuery;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CharacterController implements the CRUD actions for Character model.
 */
final class CharacterController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'create-sheet', 'index', 'update', 'view', 'load-data'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create-sheet' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all characters
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Character::canUserIndexThem()) {
            Character::throwExceptionAboutIndex();
        }

        $searchModel = new CharacterQuery(16);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single character
     * @param string $id
     * @return mixed
     * @throws HttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (empty(Yii::$app->params['activeEpic'])) {
            throw new HttpException(412, strip_tags(Yii::t('app', 'ERROR_NO_EPIC_ACTIVE')));
        }

        if (!$model->canUserViewYou()) {
            Character::throwExceptionAboutView();
        }

        if (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        if ($model->external_data_pack_id) {
            $externalDataDataProvider = $model->externalDataPack->getExternalDataAll();
        } else {
            $externalDataDataProvider = new ArrayDataProvider([]);
        }

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
            'externalDataDataProvider' => $externalDataDataProvider,
        ]);
    }

    /**
     * Creates a new character
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Character::canUserCreateThem()) {
            Character::throwExceptionAboutCreate();
        }

        $model = new Character();

        $epicListForSelector = EpicQuery::getListOfEpicsForSelector();

        $model->setCurrentEpicOnEmpty();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->character_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'epicListForSelector' => $epicListForSelector,
            ]);
        }
    }

    /**
     * Creates a new character
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return mixed
     */
    public function actionCreateSheet($id)
    {
        if (!CharacterSheet::canUserCreateThem()) {
            CharacterSheet::throwExceptionAboutCreate();
        }

        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            Character::throwExceptionAboutView();
        }

        $sheet = CharacterSheet::createForCharacter($model);

        if ($sheet) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'CHARACTER_SHEET_CREATE_FROM_CHARACTER_SUCCESS'));
            if (!$model->character_sheet_id) {
                $model->character_sheet_id = $sheet->character_sheet_id;
                $model->save();
            }
            $sheet->currently_delivered_character_id = $model->character_id;
            $sheet->save();
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'CHARACTER_SHEET_CREATE_FROM_CHARACTER_FAILURE'));
        }

        return $this->redirect(['view', 'id' => $model->character_id]);
    }

    /**
     * Updates an existing character
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            Character::throwExceptionAboutControl();
        }

        $epicListForSelector = EpicQuery::getListOfEpicsForSelector();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->character_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'epicListForSelector' => $epicListForSelector,
            ]);
        }
    }

    /**
     * Loads data from an external source
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     * @return mixed
     */
    public function actionLoadData($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            Character::throwExceptionAboutControl();
        }

        $baseUrl = $model->epic->parameterPack->getParameterValueByCode(Parameter::DATA_SOURCE_FOR_REPUTATION);

        if (!$baseUrl) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'EXTERNAL_DATA_LOAD_MISSING_ADDRESS'));
        } elseif (strpos($baseUrl, '{modelKey}') === false) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'EXTERNAL_DATA_LOAD_MISSING_MODEL_KEY'));
        } else {
            $placeholders = ['{modelKey}'];
            $data = [$model->key];

            $url = str_replace($placeholders, $data, $baseUrl);

            try {
                $retriever = new Retriever($url);
                $data = $retriever->getDataAsArray();

                if (!isset($data['content'])) {
                    throw new Exception(Yii::t('external', 'EXTERNAL_DATA_MALFORMED_ARRAY'));
                }

                /* Save external data to `data` field as data blob */
                $model->data = json_encode($data['content']);

                if ($model->save()) {

                    $loadingErrors = [];

                    /* Save external data to separate containers */
                    foreach ($data['content'] as $key => $dataRow) {
                        $result = $model->externalDataPack->saveExternalData($key, $dataRow);
                        if (!$result) {
                            $loadingErrors[] = $key;
                        }
                    }

                    if ($loadingErrors) {
                        Yii::$app->session->setFlash('error', Yii::t(
                            'external',
                            'EXTERNAL_DATA_LOAD_ERROR_PARTITION {errors}',
                            ['errors' => implode(', ', $loadingErrors)]
                        ));
                    } else {
                        Yii::$app->session->setFlash('success', Yii::t('external', 'EXTERNAL_DATA_LOAD_SUCCESS'));
                    }
                } else {
                    $errors = [];

                    foreach ($model->getErrors() as $error) {
                        $errors[] = implode(', ', $error);
                    }

                    Yii::$app->session->setFlash(
                        'error', Yii::t('external', 'EXTERNAL_DATA_LOAD_ERROR_SAVE') . ': ' . implode(', ', $errors)
                    );
                }

            } catch (Exception $e) {
                Yii::$app->session->setFlash(
                    'error',
                    Yii::t('external', 'EXTERNAL_DATA_LOAD_ERROR_JSON') . ': ' . $e->getMessage()
                );
            }
        }

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Character model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Character the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Character::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'PAGE_NOT_FOUND'));
        }
    }
}
