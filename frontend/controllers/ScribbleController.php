<?php

namespace frontend\controllers;

use common\models\Scribble;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

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
                        'reverse-favorite' => ['PATCH'],
                        'set-favorite' => ['PATCH'],
                        'unset-favorite' => ['PATCH'],
                    ],
                ],
            ]
        );
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionReverseFavorite(): void
    {
        $key = Yii::$app->request->post('key');
        $scribble = $this->getModelWithValidation($key);
        $scribble->favorite = !$scribble->favorite;
        if (!$scribble->save()) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionSetAsFavorite(): void
    {
        $key = Yii::$app->request->post('key');
        $scribble = $this->getModelWithValidation($key);
        $scribble->favorite = true;
        if (!$scribble->save()) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionUnsetAsFavorite(): void
    {
        $key = Yii::$app->request->post('key');
        $scribble = $this->getModelWithValidation($key);
        $scribble->favorite = false;
        if (!$scribble->save()) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * @throws HttpException
     */
    private function getModelWithValidation(string $key): Scribble
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $scribble = $this->findModel($key);

        if (!$scribble->isOwnedBy(Yii::$app->getUser())) {
            throw new ForbiddenHttpException(Yii::t('app', 'SCRIBBLE_DENIED_ACCESS'));
        }

        if (!$scribble->scribblePack->canUserReadYou()) {
            throw new ForbiddenHttpException(Yii::t('app', 'SCRIBBLE_DENIED_ACCESS'));
        }

        return $scribble;
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(string $key): Scribble
    {
        if (($model = Scribble::findOne(['key' => $key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'SCRIBBLE_NOT_FOUND'));
    }
}
