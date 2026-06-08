<?php

use common\models\core\Visibility;
use common\models\ExternalData;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model ExternalData */
/* @var $form ActiveForm */
?>

<div class="external-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <p><?= '<b>' . $model->getAttributeLabel('code') . ':</b> ' . $model->code ?></p>

    <?= $form
        ->field($model, 'visibility')
        ->dropDownList(Visibility::visibilityNames(Visibility::allowedVisibilities));
    ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
