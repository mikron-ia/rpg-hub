<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use backend\controllers\tools\MarkChangeTrait;
use common\models\Character;
use common\models\CharacterQuery;
use common\models\CharacterSheet;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\EpicQuery;
use common\models\Parameter;
use common\models\tools\Retriever;
use Yii;
use yii\base\Exception;
use yii\base\InvalidRouteException;
use yii\data\ArrayDataProvider;
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
    use MarkChangeTrait;

    private const POSITIONS_PER_PAGE = 16;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'create-sheet', 'index', 'update', 'view', 'load-data', 'mark-changed'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-sheet' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all characters
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

        if (!Character::canUserIndexThem()) {
            Character::throwExceptionAboutIndex();
        }

        $searchModel = new CharacterQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForOperator(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single character
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Character::throwExceptionAboutView();
        }

        if ($model->external_data_pack_id) {
            $externalDataDataProvider = $model->externalDataPack->getExternalDataAll();
        } else {
            $externalDataDataProvider = new ArrayDataProvider([]);
        }

        return $this->render('view', [
            'model' => $model,
            'externalDataDataProvider' => $externalDataDataProvider,
        ]);
    }

    /**
     * Creates a new character
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Character::canUserCreateThem()) {
            Character::throwExceptionAboutCreate();
        }

        $model = new Character();

        $epicListForSelector = EpicQuery::getListOfEpicsForSelector();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('create', [
            'model' => $model,
            'epicListForSelector' => $epicListForSelector,
        ]);
    }

    /**
     * Creates a new character
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreateSheet(string $key): Response
    {
        if (!CharacterSheet::canUserCreateThem()) {
            CharacterSheet::throwExceptionAboutCreate();
        }

        $model = $this->findModelByKey($key);

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

        return $this->redirect(['view', 'key' => $model->key]);
    }

    /**
     * Updates an existing character
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            Character::throwExceptionAboutControl();
        }

        $epicListForSelector = EpicQuery::getListOfEpicsForSelector();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
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
     * @param string $key
     *
     * @return Response|\yii\console\Response
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws InvalidRouteException
     */
    public function actionLoadData(string $key): Response|\yii\console\Response
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            Character::throwExceptionAboutControl();
        }

        $baseUrl = $model->epic->parameterPack->getParameterValueByCode(Parameter::DATA_SOURCE_FOR_REPUTATION);

        if (!$baseUrl) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'EXTERNAL_DATA_LOAD_MISSING_ADDRESS'));
        } elseif (!str_contains($baseUrl, '{modelKey}')) {
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
                    foreach ($data['content'] as $externalDataKey => $dataRow) {
                        $result = $model->externalDataPack->saveExternalData($externalDataKey, $dataRow);
                        if (!$result) {
                            $loadingErrors[] = $externalDataKey;
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
     * Saves the model to mark it as changed
     *
     * @param string $key
     *
     * @return Response
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionMarkChanged(string $key): Response
    {
        $model = $this->findModelByKey($key);
        $this->markChange($model);

        return $this->redirect(['view', 'key' => $model->key]);
    }

    /**
     * Finds the Character model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     */
    protected function findModelByKey(string $key): Character
    {
        $model = Character::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }

        return $model;
    }
}
