<?php

use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX');
?>
<div class="group-membership-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'MEMBERSHIP_BUTTON_ADD'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'group.name',
            'character.name',
            'visibility',
            'short_text',
            ['class' => ActionColumn::class],
        ],
    ]); ?>
</div>
