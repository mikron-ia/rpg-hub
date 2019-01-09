<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Epic */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="epic-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-12">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'system')->textInput(['maxlength' => true]) ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
        <div class="col-md-12">
            <?= $form->field($model, 'status')->dropDownList($model->getAllowedChangeNames()) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
