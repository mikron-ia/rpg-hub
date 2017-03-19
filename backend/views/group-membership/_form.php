<?php

use common\models\core\Visibility;
use common\models\GroupMembership;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="group-membership-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'character_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'visibility')->dropDownList(Visibility::visibilityNames(GroupMembership::allowedVisibilities())); ?>

    <?= $form->field($model, 'short_text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'public_text')->textarea(['rows' => 8]) ?>

    <?= $form->field($model, 'private_text')->textarea(['rows' => 8]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
