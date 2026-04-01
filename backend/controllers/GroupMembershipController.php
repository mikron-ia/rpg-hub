<?php

namespace backend\controllers;

use common\models\Character;
use common\models\CharacterQuery;
use common\models\Group;
use common\models\GroupMembershipHistory;
use Yii;
use common\models\GroupMembership;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * GroupMembershipController implements the CRUD actions for the GroupMembership model.
 */
final class GroupMembershipController extends CmsController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'view', 'move-up', 'move-down', 'history'],
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
     * @throws HttpException
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): Response|string
    {
        $model = $this->findModel($key);

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
     * @throws Exception
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionCreate(string $groupKey): Response|string
    {
        $model = new GroupMembership();

        $group = Group::findOne(['key' => $groupKey]);

        if (!$group) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_GROUP_NOT_FOUND'));
            return $this->returnToReferrer(['site/index']);
        } elseif (!$group->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_GROUP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if (!Group::canUserCreateThem() || !Character::canUserCreateThem()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $success = $model->load(Yii::$app->request->post());
        $model->group_id = $group->group_id;
        $success = $success && $model->save();

        if ($success) {
            return $this->returnToReferrer(['site/index']);
        } else {
            $charactersForMembership = CharacterQuery::listEpicCharactersAsArray();

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('create',
                    ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
            } else {
                return $this->render('create',
                    ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
            }
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

        if (!$model->group->canUserControlYou() || !$model->character->canUserControlYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToReferrer(['site/index']);
        } else {
            $charactersForMembership = CharacterQuery::listEpicCharactersAsArray();

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('update',
                    ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
            } else {
                return $this->render('update',
                    ['model' => $model, 'charactersForMembership' => $charactersForMembership]);
            }
        }
    }

    /**
     * @throws HttpException
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionHistory(string $key): Response|string
    {
        $model = $this->findModel($key);

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
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveUp(string $key): Response
    {
        $model = $this->findModelById($key);

        $model->movePrev();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveDown(string $key): Response
    {
        $model = $this->findModel($key);

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
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelById(string $id): GroupMembership
    {
        if (($model = GroupMembership::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(string $key): GroupMembership
    {
        if (($model = GroupMembership::findOne(['key' => $key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'GROUP_MEMBERSHIP_NOT_AVAILABLE'));
    }
}
