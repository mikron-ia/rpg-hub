<?php

use common\models\PerformedAction;
use common\models\PerformedActionQuery;
use common\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model PerformedActionQuery */
/* @var $form ActiveForm */
?>

<div class="performed-action-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id')->widget(
        Select2::class,
        ['data' => User::getFullUserList(), 'options' => ['multiple' => true]]
    )->label(Yii::t('app', 'USER_LABEL')) ?>

    <?= $form->field($model, 'class') ?>

    <?= $form->field($model, 'operation')->widget(
        Select2::class,
        ['data' => PerformedAction::actionNames(), 'options' => ['multiple' => true]]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
