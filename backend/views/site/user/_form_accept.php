<?php

use common\models\core\Language;
use common\models\User;
use kartik\password\PasswordInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model User */
/* @var $form ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'username') ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'email') ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-md-6">
        <?= $form->field($model, 'password')->widget(
            PasswordInput::class,
            [
                'pluginOptions' => [
                    'showMeter' => true,
                    'toggleMask' => false,
                ]
            ]
        ) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'password_again')->passwordInput() ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-md-3">
        <?= $form->field($model, 'language')->dropDownList(Language::languagesLong()) ?>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SAVE'), ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
