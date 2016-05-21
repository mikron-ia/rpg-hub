<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Recap */
/* @var $form yii\widgets\ActiveForm */
/* @var $epicListForSelector string[] */
?>

<div class="recap-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-lg-6">
        <?= $form->field($model, 'epic_id')->dropDownList($epicListForSelector); ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'time')->textInput() ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-12">
        <?= $form->field($model, 'data')->textarea(['rows' => 8]) ?>
    </div>

    <div class="form-group col-lg-2">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
