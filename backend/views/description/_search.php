<?php

use common\models\core\Language;
use common\models\core\Visibility;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DescriptionQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="description-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'description_pack_id') ?>

    <?= $form->field($model, 'code')->widget(
        kartik\select2\Select2::className(),
        [
            'data' => \common\models\Description::typeNames(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <?= $form->field($model, 'text') ?>

    <?= $form->field($model, 'public_text') ?>

    <?= $form->field($model, 'lang')->widget(
        kartik\select2\Select2::className(),
        [
            'data' => Language::languagesLong(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <?= $form->field($model, 'visibility')->widget(
        kartik\select2\Select2::className(),
        [
            'data' => Visibility::visibilityNames(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
