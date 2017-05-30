<?php

use common\models\CharacterSheetQuery;
use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use common\models\EpicQuery;
use common\models\Character;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Character */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="person-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'epic_id')->dropDownList(EpicQuery::getListOfEpicsForSelector()); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'tagline')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'visibility')->dropDownList(Visibility::visibilityNames(Character::allowedVisibilities())) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'importance')->dropDownList(ImportanceCategory::importanceNames()) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'character_sheet_id')->dropDownList(
            CharacterSheetQuery::getListOfCharactersForSelector(),
            ['prompt' => ' --- ' . Yii::t('app', 'CHARACTER_SHEET_PROMPT') . ' --- ']
        ) ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
        <div class="col-md-12">
            <?= $form->field($model, 'data')->textarea(['rows' => 8]) ?>
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
