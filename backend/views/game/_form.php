<?php

use backend\assets\GameAsset;
use common\models\EpicQuery;
use common\models\Game;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

GameAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4 col-lg-4">
        <?= $form->field($model, 'epic_id')->dropDownList(EpicQuery::getListOfEpicsForSelector()); ?>
    </div>

    <div class="col-md-4 col-lg-4">
        <?= $form->field($model, 'status')
            ->dropDownList($model->isNewRecord ? Game::statusNames() : $model->getAllowedChangeNames())
        ?>
    </div>

    <div class="col-md-4 col-lg-4">
        <?= $form->field($model, 'planned_date')->widget(
            DatePicker::class,
            [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd', // @todo Change to 'DD, yyyy-mm-dd' once #296 is resolved
                    'autoclose' => true,
                    'weekStart' => 1,
                    'todayHighlight' => true,
                ]
            ]
        ) ?>
    </div>

    <div class="col-md-5">
        <div class="form-group field-game-basics-constructed">
            <label class="control-label" for="game-basics-constructed">
                <?= Yii::t('app', 'GAME_BASICS_CONSTRUCTED') ?>
            </label>
            <input class="form-control" id="game-basics-constructed" disabled/>
        </div>
    </div>

    <div class="col-md-1">
        <div class="form-group">
            <label class="control-label" for="game-basics-transfer">&nbsp;</label>
            <?= Html::button('=>', [
                'class' => 'btn btn-default btn-wide',
                'id' => 'game-basics-transfer',
                'title' => Yii::t('app', 'GAME_BASICS_TRANSFER_TITLE')
            ]) ?>
        </div>
    </div>

    <div class="col-md-6">
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
