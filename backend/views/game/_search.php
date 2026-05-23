<?php

use common\models\Epic;
use common\models\GameQuery;
use common\models\state\GameStatus;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model GameQuery */
/* @var $form ActiveForm */
/* @var $epic Epic */
?>

<div class="game-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index', 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'basics')->hint('') ?>

    <?php echo $form->field($model, 'planned_location')->hint('') ?>

    <?= $form->field($model, 'status')->widget(
        Select2::class,
        ['data' => GameStatus::namesForDropdown(), 'options' => ['multiple' => true]]
    ) ?>

    <?php echo $form->field($model, 'notes')->hint('') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
