<?php

use common\models\Epic;
use common\models\Game;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GameQuery */
/* @var $form yii\widgets\ActiveForm */
/* @var $epic Epic */
?>

<div class="game-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index', 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'time') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'status')->widget(
        Select2::class,
        ['data' => Game::statusNames(), 'options' => ['multiple' => true]]
    ) ?>

    <?php echo $form->field($model, 'details') ?>

    <?php echo $form->field($model, 'note') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
