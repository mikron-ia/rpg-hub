<?php

use common\models\core\UserStatus;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel common\models\UserQuery */

$this->title = Yii::t('app', 'USER_INDEX_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INVITE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INVITATIONS'), ['invitations'], ['class' => 'btn btn-default']) ?>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'columns' => [
                'username',
                'email:email',
                [
                    'attribute' => 'role',
                    'label' => Yii::t('app', 'USER_ROLE_NAME'),
                    'value' => fn(User $model) => $model->getUserRoleName(),
                ],
                [
                    'attribute' => 'status',
                    'label' => Yii::t('app', 'USER_STATUS_LABEL'),
                    'value' => fn(User $model) => UserStatus::from($model->status)->getName(),
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, User $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['user/view', 'id' => $model->id]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>
