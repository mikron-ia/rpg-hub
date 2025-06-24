<?php

use common\models\core\UserStatus;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'USER_INDEX_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INVITE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INVITATIONS'), ['invitations'], ['class' => 'btn btn-default']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
                'contentOptions' => ['class' => 'text-center'],
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
</div>
