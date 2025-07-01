<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Announcement $model */
/** @var yii\widgets\ActiveForm $form */

$datePickerConfig = [
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'autoclose' => true,
        'weekStart' => 1,
        'todayHighlight' => true,
    ],
];
?>

<div class="announcement-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-12">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'text_raw')->textarea(['rows' => 6]) ?>
    </div>

    <div class="col-md-6">
        <?= ''; //$form->field($model, 'visible_from')->textInput()->widget(DatePicker::class, $datePickerConfig) ?>
    </div>

    <div class="col-md-6">
        <?= ''; //$form->field($model, 'visible_to')->textInput()->widget(DatePicker::class, $datePickerConfig) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SAVE'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
