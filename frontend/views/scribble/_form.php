<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Scribble $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="scribble-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'scribble_pack_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'favorite')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
