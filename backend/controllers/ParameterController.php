<?php

namespace backend\controllers;

use common\models\core\Visibility;
use common\models\Parameter;
use Throwable;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ParameterController implements the CRUD actions for the Parameter model.
 */
final class ParameterController extends Controller
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
    public function actionView(string $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(int $pack_id): Response|string
    {
        $model = new Parameter();

        $model->parameter_pack_id = $pack_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $referrer = Yii::$app->getRequest()->getReferrer();
            if ($referrer) {
                return Yii::$app->getResponse()->redirect($referrer);
            } else {
                return $this->redirect(['index']);
            }
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('create', ['model' => $model]);
            } else {
                return $this->render('create', ['model' => $model]);
            }
        }
    }

    /**
     * @throws Exception
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id): Response|string
    {
        $model = $this->findModel($id);

        if (!$model->parameterPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_PARAMETER_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $referrer = Yii::$app->getRequest()->getReferrer();
            if ($referrer) {
                return Yii::$app->getResponse()->redirect($referrer);
            } else {
                return $this->redirect(['index']);
            }
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
    public function actionDelete(string $id): Response
    {
        $this->findModel($id)->delete();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Moves parameter up in order
     *
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveUp(int $id): Response
    {
        $model = $this->findModel($id);
        $model->movePrev();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Moves parameter down in order
     *
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveDown(int $id): Response
    {
        $model = $this->findModel($id);
        $model->moveNext();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Parameter model based on its primary key value.
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(string $id): Parameter
    {
        $model = Parameter::findOne(['parameter_id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'PARAMETER_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->parameterPack->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'PARAMETER_NOT_AVAILABLE'));
        }

        return $model;
    }

    /**
     * Provides return to the referrer page; if the referrer is empty, the default value is used
     *
     * @param string[] $default
     *
     * @return Response
     *
     * @throws InvalidRouteException
     */
    protected function returnToReferrer(array $default):Response
    {
        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect($default);
        }
    }
}
