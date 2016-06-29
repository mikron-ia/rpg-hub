<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ParameterQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parameter-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'parameter_pack_id') ?>

    <?= $form->field($model, 'code')->widget(
        kartik\select2\Select2::className(),
        [
            'data' => \common\models\Parameter::typeNames(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <?= $form->field($model, 'visibility')->widget(
        kartik\select2\Select2::className(),
        [
            'data' => \common\models\core\Visibility::visibilityNames(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
