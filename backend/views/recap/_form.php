<?php

use common\models\EpicQuery;
use common\models\PointInTimeQuery;
use common\models\Recap;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Recap */
/* @var $form ActiveForm */
?>

<div class="recap-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'epic_id')->widget(
            Select2::class,
            ['data' => EpicQuery::getListOfEpicsForSelector()]
        ); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'point_in_time_id')->widget(
            Select2::class,
            ['data' => PointInTimeQuery::getListOfPointsInTimeForSelector()]
        ); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'content')->textarea(['rows' => 8]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'notes')->textarea(['rows' => 4]) ?>
    </div>

    <div class="form-group col-md-2">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
