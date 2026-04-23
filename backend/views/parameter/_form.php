<?php

use common\models\core\Visibility;
use common\models\Parameter;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Parameter */
/* @var $form ActiveForm */
/* @var $creatorController string */
/* @var $creatorKey string */
?>

<div class="parameter-form">

    <?php $form = ActiveForm::begin([
        'id' => 'story-parameter-form',
        'action' => $model->isNewRecord ?
            [
                $creatorController . '/create-parameter',
                'key' => $creatorKey,
            ] :
            [
                'parameter/update',
                'key' => $model->key,
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
