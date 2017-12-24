<?php

use common\models\EpicQuery;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PointInTime */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="point-in-time-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'epic_id')->dropDownList(EpicQuery::getListOfEpicsForSelector()); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'status'); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'text_public')->textarea(['rows' => 2]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'text_protected')->textarea(['rows' => 2]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'text_private')->textarea(['rows' => 2]) ?>
    </div>

    <div class="form-group col-md-2">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
