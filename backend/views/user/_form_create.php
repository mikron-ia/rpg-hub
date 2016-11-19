<?php

use common\models\core\Language;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'email') ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'language')->dropDownList(Language::languagesLong()) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'user_role')->dropDownList(User::userRoleNames()) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'note') ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'message')->textarea(['rows' => 12]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEND_INVITATION'), ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
