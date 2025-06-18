<?php

use common\models\core\Language;
use common\models\core\Visibility;
use common\models\Description;
use common\models\PointInTimeQuery;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="description-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-12">
        <?= $form->field($model, 'code')->dropDownList(
            $model->typeNamesForThisClass(),
            ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_TYPE') . ' --- ']
        ); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'public_text')->textarea(['rows' => 8]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'protected_text')->textarea(['rows' => 8]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'private_text')->textarea(['rows' => 8]); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'lang')->dropDownList(Language::languagesLong()); ?>
    </div>

    <div class="col-md-6">
        <?= $form
            ->field($model, 'visibility')
            ->dropDownList(Visibility::visibilityNames(Description::allowedVisibilities()));
        ?>
    </div>

    <div class="col-md-6">
        <?= $form
            ->field($model, 'point_in_time_start_id')
            ->dropDownList(
                PointInTimeQuery::getListOfPointsInTimeForSelector(),
                ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_POINT_IN_TIME') . ' --- '],
            ); ?>
    </div>

    <div class="col-md-6">
        <?= $form
            ->field($model, 'point_in_time_end_id')
            ->dropDownList(
                PointInTimeQuery::getListOfPointsInTimeForSelector(),
                ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_POINT_IN_TIME') . ' --- '],
            ); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
