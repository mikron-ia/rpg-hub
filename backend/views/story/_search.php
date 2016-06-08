<?php

use common\models\EpicQuery;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'epic_id')->widget(
        kartik\select2\Select2::className(),
        [
            'data' => EpicQuery::getListOfEpicsForSelector(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'descriptions') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
