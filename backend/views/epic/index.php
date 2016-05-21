<?php

use common\models\Epic;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EpicQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_EPICS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
        <?= Html::a(Yii::t('app', 'BUTTON_EPIC_ADD'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'key',
                'format' => 'raw',
                'value' => function (Epic $model) {
                    return '<span class="key">' . $model->key . '</span>';
                },
            ],
            [
                'attribute' => 'name',
            ],
            [
                'attribute' => 'system',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
</div>
