<?php

use common\models\core\Visibility;
use common\models\Parameter;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Parameter */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parameter-form">

    <?php $form = ActiveForm::begin([
        'id' => 'story-parameter-form',
        'action' => $model->isNewRecord ?
            [
                'parameter/create',
                'pack_id' => $model->parameter_pack_id
            ] :
            [
                'parameter/update',
                'id' => $model->parameter_id
            ],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'code')->dropDownList(
        $model->isNewRecord ? $model->typeNamesForThisClassForAdd() : $model->typeNamesForThisClassForEdit(),
        ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_TYPE') . ' --- ']
    ); ?>

    <?= $form->field($model, 'visibility')->dropDownList(Visibility::visibilityNames(Parameter::allowedVisibilities())); ?>

    <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
