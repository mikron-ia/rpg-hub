<?php

use common\models\core\Visibility;
use common\models\EpicQuery;
use common\models\ScenarioQuery;
use common\models\Project;
use common\models\state\ProjectStatus;
use common\models\type\ProjectType;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Project */
/* @var $form ActiveForm */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'epic_id')->widget(
            Select2::class,
            ['data' => EpicQuery::getListOfEpicsForSelector()]
        ); ?>
    </div>

    <div class="col-md-3">
        <?= $form
            ->field($model, 'visibility')
            ->dropDownList(Visibility::visibilityNames(Project::allowedVisibilities())) ?>
    </div>

    <div class="col-md-3">
        <?= $form
            ->field($model, 'code')
            ->dropDownList(ProjectType::namesForDropdown()) ?>
    </div>

    <div class="col-md-6 col-lg-3">
        <?= $form
            ->field($model, 'status')
            ->dropDownList($model->isNewRecord ? ProjectStatus::listAllNamesForDropdown() : $model->getStatus()->getAllowedSuccessorsAsStrings())
        ?>
    </div>

    <div class="col-md-8">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'based_on_id')->widget(
            Select2::class,
            [
                'data' => ScenarioQuery::allFromCurrentEpicForSelector(),
                'options' => ['placeholder' => ' --- ' . Yii::t('app', 'PROJECT_FORM_SELECT_SCENARIO') . ' --- '],
                'pluginOptions' => ['allowClear' => true],
            ],
        ); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'short')->textarea(['rows' => 8]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'long')->textarea(['rows' => 16]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'notes')->textarea(['rows' => 16]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'data')->textarea(['rows' => 8]); ?>
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
