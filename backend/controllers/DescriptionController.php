<?php

namespace backend\controllers;

use common\models\core\Visibility;
use common\models\DescriptionHistory;
use common\models\DescriptionPack;
use common\models\Parameter;
use Yii;
use common\models\Description;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * DescriptionController implements the CRUD actions for Description model.
 */
final class DescriptionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'delete', 'update', 'view', 'move-up', 'move-down', 'history'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Displays a single Description model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Description model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $pack_id
     * @return mixed
     */
    public function actionCreate($pack_id)
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
        if(in_array($language, Yii::$app->params['languagesAvailable'])) {
            $model->lang = $language;
        } else {
            $model->lang = 'en';
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['site/index']);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('create', ['model' => $model]);
            } else {
                return $this->render('create', ['model' => $model]);
            }
        }
    }

    /**
     * Updates an existing Description model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->descriptionPack->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_DESCRIPTION_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['site/index']);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('update', ['model' => $model]);
            } else {
                return $this->render('update', ['model' => $model]);
            }
        }
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function actionHistory($id)
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
        } else {
            return $this->render('history', ['model' => $model, 'historyRecords' => $historyRecords]);
        }
    }

    /**
     * Deletes an existing Description model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    public function actionMoveUp($id)
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

    public function actionMoveDown($id)
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
     * Finds the Description model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Description the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
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
     * @param string[] $default
     * @return Response
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
