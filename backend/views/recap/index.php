<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'RECAP_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recap-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-right">
        <?= Html::a(Yii::t('app', 'BUTTON_RECAP_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'key',
            'name',
            'time',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
