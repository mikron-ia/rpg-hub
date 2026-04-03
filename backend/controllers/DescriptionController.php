<?php

namespace backend\controllers;

use common\models\core\Visibility;
use common\models\Description;
use common\models\DescriptionHistory;
use common\models\DescriptionPack;
use common\models\Parameter;
use Exception;
use Override;
use Throwable;
use Yii;
use yii\db\Exception as DbException;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class DescriptionController extends CmsController
{
    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'create',
                            'delete',
                            'update',
                            'view',
                            'move-up',
                            'move-down',
                            'history',
                            'display',
                            'set-as-current',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set-as-current' => ['PATCH'],
                    'delete' => ['DELETE'],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
    {
        return $this->render('view', [
            'model' => $this->findModel($key),
        ]);
    }

    /**
     * @throws DbException
     * @throws HttpException
     */
    public function actionCreate(string $packKey): Response|string
    {
        $model = new Description();

        $descriptionPack = DescriptionPack::findOne(['key' => $packKey]);

        if (!$descriptionPack) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_NO_PACK'));
            return $this->returnToReferrer(['site/index']);
        } elseif (!$descriptionPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $model->description_pack_id = $descriptionPack->description_pack_id;

        $language = $descriptionPack->getEpic()->parameterPack->getParameterValueByCode(Parameter::LANGUAGE);
        if (in_array($language, Yii::$app->params['languagesAvailable'])) {
            $model->lang = $language;
        } else {
            $model->lang = 'en';
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['site/index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', ['model' => $model]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * @throws DbException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->descriptionPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['site/index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', ['model' => $model]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionHistory(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->descriptionPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $historyRecords = DescriptionHistory::find()
            ->where(['description_id' => $model->description_id])
            ->orderBy(['created_at' => SORT_DESC]);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('history', ['model' => $model, 'historyRecords' => $historyRecords]);
        }

        return $this->render('history', ['model' => $model, 'historyRecords' => $historyRecords]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(string $key): Response
    {
        $this->findModel($key)->delete();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index']);
    }

    /**
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionMoveUp(string $key): bool
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $model = $this->findModel($key);
        return $model->movePrev();
    }

    /**
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionMoveDown(string $key): bool
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $model = $this->findModel($key);
        return $model->moveNext();
    }

    /**
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionDisplay(string $packKey): string
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $model = $this->findPack($packKey);
        return $this->renderAjax('_view_descriptions', ['model' => $model]);
    }

    /**
     * @throws DbException
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionSetAsCurrent(string $key): Response
    {
        if (!Yii::$app->request->isPatch) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_PATCH_REQUESTS_ONLY'));
        }

        $model = $this->findModel($key);

        if (!$model->descriptionPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $success = true;
        $outdateCount = 0;

        $descriptionsToCheck = Description::find()
            ->where(['description_pack_id' => $model->description_pack_id, 'code' => $model->code])
            ->all();

        foreach ($descriptionsToCheck as $description) {
            if ($description->description_id === $model->description_id) {
                $description->outdated = false;
            } else {
                $description->outdated = true;
                $outdateCount++;
            }
            $success = $success && $description->save(false);
        }

        if ($success) {
            Yii::$app->session->setFlash('success', Yii::t(
                'app',
                'DESCRIPTION_SET_AS_CURRENT_SUCCESS {count}',
                ['count' => $outdateCount])
            );
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'DESCRIPTION_SET_AS_CURRENT_FAILURE'));
        }

        return $this->returnToReferrer(['site/index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(string $key): Description
    {
        $model = Description::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'DESCRIPTION_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->descriptionPack->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'DESCRIPTION_NOT_AVAILABLE'));
        }

        return $model;
    }

    /**
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    protected function findPack(string $key): DescriptionPack
    {
        $model = DescriptionPack::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'DESCRIPTION_PACK_NOT_AVAILABLE'));
        }

        if (!$model->canUserReadYou()) {
            throw new ForbiddenHttpException(Yii::t('app', 'DESCRIPTION_PACK_NOT_ACCESSIBLE'));
        }

        return $model;
    }
}
