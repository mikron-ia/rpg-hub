<?php

use common\models\Epic;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheetQuery */
/* @var $form yii\widgets\ActiveForm */
/* @var $epic Epic */
?>

<div class="character-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index', 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
