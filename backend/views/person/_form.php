<?php

use common\models\CharacterQuery;
use common\models\EpicQuery;
use common\models\Person;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="person-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-lg-6">
        <?= $form->field($model, 'epic_id')->dropDownList(EpicQuery::getListOfEpicsForSelector()); ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-12">
        <?= $form->field($model, 'tagline')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'visibility')->dropDownList(Person::visibilityNames(), ['prompt' => 'PROMPT_VISIBILITY']) ?>
    </div>

    <div class="col-lg-6">
        <?= $form->field($model, 'character_id')->dropDownList(
            CharacterQuery::getListOfCharactersForSelector(),
            [
                'prompt' => Yii::t('app', 'CHARACTER_PROMPT')
            ]
        ) ?>
    </div>

    <?php if (!$model->isNewRecord): ?>

        <div class="col-md-12">
            <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>
        </div>

    <?php endif; ?>

    <div class="clearfix"></div>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
