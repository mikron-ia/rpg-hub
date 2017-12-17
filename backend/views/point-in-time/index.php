<?php

use common\models\PointInTime;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_POINT_IN_TIME_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-in-time-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_POINT_IN_TIME_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'position',
            'name',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {up} {down}',
                'buttons' => [
                    'up' => function ($url, PointInTime $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-up"></span>',
                            ['game/move-down', 'id' => $model->point_in_time_id],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_UP'),
                            ]
                        );
                    },
                    'down' => function ($url, PointInTime $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-down"></span>',
                            ['game/move-up', 'id' => $model->point_in_time_id],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_DOWN'),
                            ]
                        );
                    },
                ]
            ],
        ],
    ]); ?>
</div>
