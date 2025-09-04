<?php

use common\models\core\Visibility;
use common\models\EpicQuery;
use common\models\Story;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'epic_id')->widget(
            Select2::class,
            ['data' => EpicQuery::getListOfEpicsForSelector()]
        ); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'visibility')->dropDownList(Visibility::visibilityNames(Story::allowedVisibilities())) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'short')->textarea(['rows' => 10]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'long')->textarea(['rows' => 20]); ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
        <div class="col-md-12">
            <?= $form->field($model, 'is_off_the_record_change')->checkbox() ?>
        </div>
    <?php endif; ?>

    <div class="form-group col-md-2">
        <?php
        echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        );
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
