<?php

use common\models\EpicQuery;
use common\models\Game;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4 col-lg-2">
        <?= $form->field($model, 'epic_id')->dropDownList(EpicQuery::getListOfEpicsForSelector()); ?>
    </div>

    <div class="col-md-4 col-lg-2">
        <?= $form->field($model, 'status')->dropDownList(Game::statusNames()) ?>
    </div>

    <div class="col-md-4 col-lg-2">
        <?= $form->field($model, 'planned_date')->widget(
            DatePicker::class,
            [
                'language' => 'en-gb',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                    'weekStart' => 1,
                    'todayHighlight' => true,
                ]
            ]
        ) ?>
    </div>

    <div class="col-md-12 col-lg-6">
        <?= $form->field($model, 'basics')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'notes')->textarea(['rows' => 8]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'LABEL_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
