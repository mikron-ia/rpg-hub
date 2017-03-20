<?php

namespace backend\controllers;

use common\models\Character;
use common\models\CharacterQuery;
use common\models\Group;
use common\models\GroupMembershipHistory;
use Yii;
use common\models\GroupMembership;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * GroupMembershipController implements the CRUD actions for GroupMembership model.
 */
class GroupMembershipController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'view', 'move-up', 'move-down', 'history'],
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
     * Displays a single GroupMembership model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (!$model->group->canUserViewYou() || !$model->character->canUserViewYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new GroupMembership model
     * @param $group_id
     * @return mixed
     */
    public function actionCreate($group_id)
    {
        $model = new GroupMembership();

        $group = Group::findOne(['group_id' => $group_id]);

        if (!$group) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_GROUP_NOT_FOUND'));
            return $this->returnToReferrer(['site/index']);
        } elseif (!$group->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_GROUP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $model->group_id = $group_id;

        if (!Group::canUserCreateThem() || !Character::canUserCreateThem()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['site/index']);
        } else {
            $charactersForMembership = CharacterQuery::listEpicCharactersAsArray();

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('create', ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
            } else {
                return $this->render('create', ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
            }
        }
    }

    /**
     * Updates an existing GroupMembership model
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->group->canUserControlYou() || !$model->character->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['site/index']);
        } else {
            $charactersForMembership = CharacterQuery::listEpicCharactersAsArray();

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('update', ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
            } else {
                return $this->render('update', ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
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

        if (!$model->group->canUserViewYou() || !$model->character->canUserViewYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $historyRecords = GroupMembershipHistory::find()
            ->where(['group_membership_id' => $model->group_membership_id])
            ->orderBy(['created_at' => SORT_DESC]);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('history', ['model' => $model, 'historyRecords' => $historyRecords]);
        } else {
            return $this->render('history', ['model' => $model, 'historyRecords' => $historyRecords]);
        }
    }

    /**
     * @param $id
     * @return Response
     */
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

    /**
     * @param $id
     * @return Response
     */
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
     * Finds the GroupMembership model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GroupMembership the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GroupMembership::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
