<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PointInTime */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_POINT_IN_TIME_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-in-time-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'key' => $model->point_in_time_id],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_DOWN'),
            ['move-up', 'key' => $model->point_in_time_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_UP'),
            ['move-down', 'key' => $model->point_in_time_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'key' => $model->point_in_time_id],
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
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <h2><?= Yii::t('app', 'POINT_IN_TIME_NAME_PUBLIC'); ?></h2>
        <?= $model->text_public ?? '<p>' . Yii::t('app', 'NO_TEXT') . '</p>'; ?>
    </div>

    <div class="col-md-6">
        <h2><?= Yii::t('app', 'POINT_IN_TIME_NAME_PROTECTED'); ?></h2>
        <?= $model->text_protected ?? '<p>' . Yii::t('app', 'NO_TEXT') . '</p>'; ?>
    </div>

    <div class="col-md-6">
        <h2><?= Yii::t('app', 'POINT_IN_TIME_NAME_PRIVATE'); ?></h2>
        <?= $model->text_private ?? '<p>' . Yii::t('app', 'NO_TEXT') . '</p>'; ?>
    </div>

</div>
