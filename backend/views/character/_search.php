<?php

use common\models\Character;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="person-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'name') ?>

    <?php echo $form->field($model, 'tagline') ?>

    <?php echo $form->field($model, 'visibility')->widget(
        kartik\select2\Select2::className(),
        [
            'data' => Character::visibilityNames(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
