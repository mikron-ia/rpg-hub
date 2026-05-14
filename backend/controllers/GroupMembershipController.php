<?php

namespace backend\controllers;

use common\models\Character;
use common\models\CharacterQuery;
use common\models\Group;
use common\models\GroupMembershipHistory;
use Override;
use Yii;
use common\models\GroupMembership;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

final class GroupMembershipController extends CmsController
{
    #[Override]
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
        }

        return $this->render('view', ['model' => $model]);
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
        }

        $charactersForMembership = CharacterQuery::listEpicCharactersAsArray($model->group->epic);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax(
                'create',
                [
                    'model' => $model,
                    'charactersForMembership' => $charactersForMembership,
                ]
            );
        }

        return $this->render(
            'create',
            [
                'model' => $model,
                'charactersForMembership' => $charactersForMembership,
            ]
        );
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws InvalidRouteException
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
            $charactersForMembership = CharacterQuery::listEpicCharactersAsArray($model->group->epic);

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax(
                    'update',
                    ['model' => $model, 'charactersForMembership' => $charactersForMembership]
                );
            }

            return $this->render(
                'update',
                ['model' => $model, 'charactersForMembership' => $charactersForMembership]
            );
        }
    }

    /**
     * @throws HttpException
     * @throws InvalidRouteException
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
        }

        return $this->render('history', ['model' => $model, 'historyRecords' => $historyRecords]);
    }

    /**
     * @throws HttpException
     */
    public function actionMoveUp(string $key): Response
    {
        $model = $this->findModel($key);

        if (!$model->group->canUserViewYou() || !$model->character->canUserViewYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $model->movePrev();

        return $this->returnToReferrer(['index']);
    }

    /**
     * @throws HttpException
     */
    public function actionMoveDown(string $key): Response
    {
        $model = $this->findModel($key);

        if (!$model->group->canUserViewYou() || !$model->character->canUserViewYou()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_MEMBERSHIP_ACCESS_DENIED'));
            return $this->returnToReferrer(['site/index']);
        }

        $model->moveNext();

        return $this->returnToReferrer(['index']);
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
