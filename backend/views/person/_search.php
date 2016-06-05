<?php

use common\models\EpicQuery;
use common\models\Person;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PersonQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="person-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="col-md-6">
        <?php echo $form->field($model, 'epic_id')->widget(
            kartik\select2\Select2::className(),
            [
                'data' => EpicQuery::getListOfEpicsForSelector(),
                'options' => ['multiple' => true],
            ]
        ) ?>
    </div>

    <div class="col-md-6">
        <?php echo $form->field($model, 'name') ?>
    </div>

    <div class="col-md-6">
        <?php echo $form->field($model, 'tagline') ?>
    </div>

    <div class="col-md-6">
        <?php echo $form->field($model, 'visibility')->widget(
            kartik\select2\Select2::className(),
            [
                'data' => Person::visibilityNames(),
                'options' => ['multiple' => true],
            ]
        ) ?>
    </div>

    <div class="form-group">
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
