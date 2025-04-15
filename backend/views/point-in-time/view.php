<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PointInTime */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_POINT_IN_TIME_INDEX'),
    'url' => ['point-in-time/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-in-time-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'id' => $model->point_in_time_id],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_DOWN'),
            ['move-up', 'id' => $model->point_in_time_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_UP'),
            ['move-down', 'id' => $model->point_in_time_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'id' => $model->point_in_time_id],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'position',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => '<span class="table-tag ' . $model->getStatusCSS() . '">' . $model->getStatus() . '</span>',
                ]
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <h2><?= Yii::t('app', 'POINT_IN_TIME_NAME_PUBLIC'); ?></h2>
        <?= $model->text_public ?? '<p class="no-text">' . Yii::t('app', 'NO_TEXT') . '</p>'; ?>
    </div>

    <div class="col-md-6">
        <h2><?= Yii::t('app', 'POINT_IN_TIME_NAME_PROTECTED'); ?></h2>
        <?= $model->text_protected ?? '<p class="no-text">' . Yii::t('app', 'NO_TEXT') . '</p>'; ?>
    </div>

    <div class="col-md-6">
        <h2><?= Yii::t('app', 'POINT_IN_TIME_NAME_PRIVATE'); ?></h2>
        <?= $model->text_private ?? '<p class="no-text">' . Yii::t('app', 'NO_TEXT') . '</p>'; ?>
    </div>

</div>
