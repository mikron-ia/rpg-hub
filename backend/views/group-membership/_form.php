<?php

use common\models\Character;
use common\models\core\Visibility;
use common\models\GroupMembership;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */
/* @var $form yii\widgets\ActiveForm */
/* @var $charactersForMembership Character[] */
?>

<div class="group-membership-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'character_id')->dropDownList($charactersForMembership) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'visibility')->dropDownList(Visibility::visibilityNames(GroupMembership::allowedVisibilities())); ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'status')->dropDownList(GroupMembership::statusNames()) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'short_text')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'public_text')->textarea(['rows' => 4]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'private_text')->textarea(['rows' => 4]) ?>
    </div>

    <div class="form-group text-right">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
