<?php

use common\models\Epic;
use common\models\PointInTime;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $epic Epic */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_POINT_IN_TIME_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-in-time-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_POINT_IN_TIME_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'position',
            'name',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (PointInTime $model) {
                    return '<span class="table-tag ' . $model->getStatusCSS() . '">' . $model->getStatus() . '</span>';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {up} {down}',
                'buttons' => [
                    'view' => function ($url, PointInTime $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['point-in-time/view', 'id' => $model->point_in_time_id]),
                            ['title' => Yii::t('app', 'BUTTON_VIEW')]
                        );
                    },
                    'update' => function ($url, PointInTime $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl([
                                'point-in-time/update',
                                'id' => $model->point_in_time_id
                            ]),
                            ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                        );
                    },
                    'up' => function ($url, PointInTime $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-up"></span>',
                            ['point-in-time/move-down', 'id' => $model->point_in_time_id],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_UP'),
                            ]
                        );
                    },
                    'down' => function ($url, PointInTime $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-down"></span>',
                            ['point-in-time/move-up', 'id' => $model->point_in_time_id],
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
