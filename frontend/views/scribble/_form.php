<?php

use common\models\Scribble;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var Scribble $model */
/** @var ActiveForm $form */
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
