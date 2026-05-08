<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Epic;
use common\models\Image;
use common\models\ImageLink;
use common\models\ImageQuery;
use Override;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class ImageController extends CmsController
{
    use EpicAssistance;

    #[Override]
    public function behaviors(): array
    {
        return array_merge(
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => [
                                'create',
                                'index',
                                'update',
                                'view',
                                'delete',
                                'add-link',
                                'update-link',
                                'delete-link',
                                'view-link',
                            ],
                            'allow' => true,
                            'roles' => ['operator'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-link' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): string
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

        $searchModel = new ImageQuery();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModel($key);

        if (!$model->canUserViewYou()) {
            Image::throwExceptionAboutView();
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreate(?string $epic = null): Response|string
    {
        if (!Image::canUserCreateThem()) {
            Image::throwExceptionAboutCreate();
        }

        $model = new Image();
        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Image::throwExceptionAboutControl();
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws HttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Image::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionAddLink(string $imageKey): Response|string
    {
        $image = $this->findModel($imageKey);

        if (!$image->canUserControlYou()) {
            Image::throwExceptionAboutControl();
        }

        $model = new ImageLink();

        $dataLoad = $model->load(Yii::$app->request->post());
        $model->image_id = $image->image_id;

        if ($dataLoad && $model->save()) {
            return $this->redirect(['view', 'key' => $image->key]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('link/create', ['model' => $model]);
        }

        return $this->render('link/create', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionUpdateLink(string $imageLinkKey): Response|string
    {
        $model = $this->findModelLink($imageLinkKey);

        if (!$model->image->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_IMAGE_ACCESS_DENIED'));
            return $this->returnToReferrer(['image/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('link/update', ['model' => $model]);
        }

        return $this->render('link/update', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionViewLink(string $imageLinkKey): string
    {
        $model = $this->findModelLink($imageLinkKey);

        if (!$model->image->canUserViewYou()) {
            throw new HttpException(403, Yii::t('app', 'ERROR_IMAGE_ACCESS_DENIED'));
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('link/view', ['model' => $model]);
        }

        return $this->render('link/view', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws Throwable
     */
    public function actionDeleteLink(string $imageLinkKey): Response|string
    {
        $model = $this->findModelLink($imageLinkKey);

        if (!$model->image->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_IMAGE_ACCESS_DENIED'));
            return $this->returnToReferrer(['image/index']);
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'IMAGE_LINK_DELETED'));
            return $this->returnToReferrer(['image/index']);
        }

        Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_IMAGE_LINK_DELETE'));
        return $this->returnToReferrer(['image/index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(string $key): ?Image
    {
        if (($model = Image::findOne(['key' => $key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'IMAGE_NOT_AVAILABLE'));
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModelLink(string $key): ?ImageLink
    {
        if (($model = ImageLink::findOne(['key' => $key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'IMAGE_LINK_NOT_AVAILABLE'));
    }
}
