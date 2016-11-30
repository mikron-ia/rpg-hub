<?php

use common\models\EpicQuery;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="character-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'epic_id')->dropDownList(EpicQuery::getListOfEpicsForSelector()); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <?php if (!$model->isNewRecord): ?>

        <div class="col-md-6">
            <?= $form->field($model, 'currently_delivered_person_id')->dropDownList(
                $model->getPeopleAvailableToThisCharacterAsDropDownList(),
                [
                    'prompt' => ' --- ' . Yii::t('app', 'CHARACTER_SHEET_FORM_SELECT_CURRENTLY_DELIVERED_PERSON') . ' --- '
                ]
            ); ?>
        </div>

    <?php endif; ?>

    <div class="clearfix"></div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>