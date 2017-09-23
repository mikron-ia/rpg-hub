<?php

use common\models\core\Language;
use common\models\core\Visibility;
use common\models\Description;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="description-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->dropDownList(
        $model->typeNamesForThisClass(),
        ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_TYPE') . ' --- ']
    ); ?>

    <?= $form->field($model, 'public_text')->textarea(['rows' => 8]); ?>

    <?= $form->field($model, 'protected_text')->textarea(['rows' => 8]); ?>

    <?= $form->field($model, 'private_text')->textarea(['rows' => 8]); ?>

    <?= $form->field($model, 'lang')->dropDownList(Language::languagesLong()); ?>

    <?= $form->field($model, 'visibility')->dropDownList(Visibility::visibilityNames(Description::allowedVisibilities())); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
