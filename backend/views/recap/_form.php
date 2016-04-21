<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Recap */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recap-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-lg-6">
        <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'time')->textInput() ?>
    </div>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 8]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
