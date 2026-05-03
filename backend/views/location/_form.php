<?php

use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use common\models\EpicQuery;
use common\models\Location;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Location */
/* @var $form ActiveForm */
/* @var $epicListForSelector string[] */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'epic_id')->widget(
            Select2::class,
            ['data' => EpicQuery::getListOfEpicsForSelector()]
        ); ?>
    </div>

    <div class="col-md-7">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-2">
        <?= $form
            ->field($model, 'visibility')
            ->dropDownList(Visibility::visibilityNames(Location::allowedVisibilities())
            ) ?>
    </div>

    <div class="col-md-10">
        <?= $form->field($model, 'tagline')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'importance_category')->dropDownList(ImportanceCategory::importanceNames()) ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
        <div class="col-md-12">
            <?= $form->field($model, 'is_off_the_record_change')->checkbox() ?>
        </div>
    <?php endif; ?>

    <div class="clearfix"></div>

    <div class="form-location">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
