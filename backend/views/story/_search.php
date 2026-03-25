<?php

use common\models\Epic;
use common\models\StoryQuery;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model StoryQuery */
/* @var $form ActiveForm */
/* @var $epic Epic */
?>

<div class="story-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index', 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'descriptions') ?>

    <?= $form->field($model, 'parameters') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
