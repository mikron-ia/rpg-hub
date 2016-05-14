<?php

use common\models\StoryParameter;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryParameter */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-form">

    <?php $form = ActiveForm::begin([
        'id' => 'story-parameter-form',
        'action' => $model->isNewRecord ?
            [
                'parameter-create',
                'story_id' => $model->story_id
            ] :
            [
                'parameter-update',
                'id' => $model->story_parameter_id
            ],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'code')->dropDownList(StoryParameter::codeNames()) ?>

    <?= $form->field($model, 'visibility')->dropDownList(StoryParameter::visibilityNames()) ?>

    <?= $form->field($model, 'content') ?>

    <div class="form-group text-right">
        <?php
        echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        );
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
