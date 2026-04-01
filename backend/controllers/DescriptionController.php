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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @throws DbException
     * @throws HttpException
     */
    public function actionCreate(int $pack_id): Response|string
    {
        $model = new Description();

        $descriptionPack = DescriptionPack::findOne(['description_pack_id' => $pack_id]);

        if (!$descriptionPack) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_NO_PACK'));
            return $this->returnToReferrer(['site/index']);
        } elseif (!$descriptionPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $model->description_pack_id = $pack_id;

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
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

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
    public function actionHistory(int $id): Response|string
    {
        $model = $this->findModel($id);

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
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

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
    public function actionMoveUp(int $id): bool
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $model = $this->findModel($id);
        return $model->movePrev();
    }

    /**
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionMoveDown(int $id): bool
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $model = $this->findModel($id);
        return $model->moveNext();
    }

    /**
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionDisplay(int $id): string
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $model = $this->findPack($id);
        return $this->renderAjax('_view_descriptions', ['model' => $model]);
    }

    /**
     * @throws DbException
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionSetAsCurrent(int $id): Response
    {
        if (!Yii::$app->request->isPatch) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_PATCH_REQUESTS_ONLY'));
        }

        $model = $this->findModel($id);

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
    protected function findModel(int $id): Description
    {
        $model = Description::findOne(['description_id' => $id]);

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
    protected function findPack(int $id): DescriptionPack
    {
        $model = DescriptionPack::findOne(['description_pack_id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'DESCRIPTION_PACK_NOT_AVAILABLE'));
        }

        if (!$model->canUserReadYou()) {
            throw new ForbiddenHttpException(Yii::t('app', 'DESCRIPTION_PACK_NOT_ACCESSIBLE'));
        }

        return $model;
    }
}
