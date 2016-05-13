<?php

use common\models\StoryParameter;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryParameter */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-lg-12">
        <?= $form->field($model, 'story_id'); ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'code')->dropDownList(StoryParameter::codeNames()) ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'visibility')->dropDownList(StoryParameter::visibilityNames()) ?>
    </div>

    <div class="col-lg-12">
        <?= $form->field($model, 'content')->textarea(['rows' => 20]) ?>
    </div>

    <div class="form-group col-lg-2">
        <?php
        echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        );
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
