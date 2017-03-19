<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-membership-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Group Membership'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'group_membership_id',
            'character_id',
            'group_id',
            'visibility',
            'position',
            // 'short_text',
            // 'public_text:ntext',
            // 'private_text:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
