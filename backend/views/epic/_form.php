<?php

use common\models\core\FrontStyles;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Epic */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="epic-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'system')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'style')->widget(Select2::class, ['data' => FrontStyles::provideStyleNames()]); ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
        <div class="col-md-6">
            <?= $form->field($model, 'current_story_id')->widget(
                Select2::class,
                [
                    'data' => $model->getAllowedStoriesForDropDown(),
                    'options' => ['placeholder' => ' --- ' . Yii::t('app', 'EPIC_SELECT_CURRENT_STORY') . ' --- '],
                    'pluginOptions' => ['allowClear' => true],
                ]
            ); ?>
        </div>

        <div class="col-md-6">
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
