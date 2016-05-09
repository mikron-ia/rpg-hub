<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RecapQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recap-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group col-lg-6">
        <?= $form->field($model, 'key') ?>
    </div>

    <div class="form-group col-lg-6">
        <?= $form->field($model, 'name') ?>
    </div>

    <div class="form-group col-lg-6">
        <?= $form->field($model, 'data') ?>
    </div>

    <div class="form-group col-lg-6">
        <?= $form->field($model, 'time') ?>
    </div>

    <div class="form-group col-lg-4">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
