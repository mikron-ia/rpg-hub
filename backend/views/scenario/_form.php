<?php

use common\models\EpicQuery;
use common\models\Scenario;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Scenario */
/* @var $form ActiveForm */
?>

<div class="scenario-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'epic_id')->widget(
            Select2::class,
            ['data' => EpicQuery::getListOfEpicsForSelector()]
        ); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'status')->dropDownList(Scenario::statusNames()) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'tag_line')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'content')->textarea(['rows' => 8]); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
