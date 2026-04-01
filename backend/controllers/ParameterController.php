<?php

namespace backend\controllers;

use common\models\core\Visibility;
use common\models\Parameter;
use common\models\ParameterPack;
use Throwable;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ParameterController implements the CRUD actions for the Parameter model.
 */
final class ParameterController extends CmsController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'delete', 'update', 'view', 'move-up', 'move-down'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
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
     * @throws Exception
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionCreate(string $packKey): Response|string
    {
        $model = new Parameter();

        $pack = $this->findPackModel($packKey);

        $success = $model->load(Yii::$app->request->post());
        $model->parameter_pack_id = $pack->parameter_pack_id;
        $success = $success && $model->save();

        if ($success) {
            return $this->returnToReferrer(['index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', ['model' => $model]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->parameterPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_PARAMETER_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['index']);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('update', ['model' => $model]);
            } else {
                return $this->render('update', ['model' => $model]);
            }
        }
    }

    /**
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(string $key): Response
    {
        $this->findModel($key)->delete();

        return $this->returnToReferrer(['index']);
    }

    /**
     * Moves parameter up in order
     *
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveUp(string $key): Response
    {
        $this->findModel($key)->movePrev();

        return $this->returnToReferrer(['index']);
    }

    /**
     * Moves parameter down in order
     *
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveDown(string $key): Response
    {
        $this->findModel($key)->moveNext();

        return $this->returnToReferrer(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(string $key): Parameter
    {
        $model = Parameter::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'PARAMETER_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->parameterPack->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'PARAMETER_NOT_AVAILABLE'));
        }

        return $model;
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findPackModel(string $key): ParameterPack
    {
        $model = ParameterPack::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'PARAMETER_PACK_NOT_AVAILABLE'));
        }

        return $model;
    }
}
