<?php

use common\models\Announcement;
use common\models\EpicQuery;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var Announcement $model */
/** @var ActiveForm $form */

$datePickerConfig = [
    'pluginOptions' => [
        'autoclose' => true,
        'weekStart' => 1,
        'todayHighlight' => true,
    ],
];
?>

<div class="announcement-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-5">
        <?= $form->field($model, 'epic_id')->dropDownList(EpicQuery::getListOfEpicsForSelector()); ?>
    </div>

    <div class="col-md-7">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'text_raw')->textarea(['rows' => 6]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'visible_from')->textInput()->widget(DateTimePicker::class, $datePickerConfig) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'visible_to')->textInput()->widget(DateTimePicker::class, $datePickerConfig) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SAVE'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
