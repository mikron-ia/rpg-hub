<?php

namespace frontend\controllers;

use common\models\Scribble;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * ScribbleController implements the CRUD actions for Scribble model.
 */
class ScribbleController extends Controller
{
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => [
                                'reverse-favorite',
                                'set-favorite',
                                'unset-favorite',
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionReverseFavorite(int $id): void
    {
        $scribble = $this->getModelWithValidation($id);
        $scribble->favorite = !$scribble->favorite;
        if (!$scribble->save()) {
            throw new ServerErrorHttpException();
        }
    }

    public function actionSetAsFavorite(int $id): void
    {
        $scribble = $this->getModelWithValidation($id);
        $scribble->favorite = true;
        if (!$scribble->save()) {
            throw new ServerErrorHttpException();
        }
    }

    public function actionUnsetAsFavorite(int $id): void
    {
        $scribble = $this->getModelWithValidation($id);
        $scribble->favorite = false;
        if (!$scribble->save()) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * @param int $scribbleId
     *
     * @return Scribble
     *
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    private function getModelWithValidation(int $scribbleId): Scribble
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $scribble = $this->findModel($scribbleId);

        if (!$scribble->isOwnedBy(Yii::$app->getUser())) {
            throw new ForbiddenHttpException(Yii::t('app', 'SCRIBBLE_DENIED_ACCESS'));
        }

        if (!$scribble->scribblePack->canUserReadYou()) {
            throw new ForbiddenHttpException(Yii::t('app', 'SCRIBBLE_DENIED_ACCESS'));
        }

        return $scribble;
    }

    /**
     * Finds the Scribble model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $scribble_id Scribble ID
     *
     * @return Scribble the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $scribble_id): Scribble
    {
        if (($model = Scribble::findOne(['scribble_id' => $scribble_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'SCRIBBLE_NOT_FOUND'));
    }
}
