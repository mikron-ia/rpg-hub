<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ExternalData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <p><?= '<b>' . $model->getAttributeLabel('code') . ':</b> ' . $model->code ?></p>

    <?= $form->field($model, 'visibility')->dropDownList(\common\models\core\Visibility::visibilityNames()); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
