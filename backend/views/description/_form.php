<?php

use common\models\core\Language;
use common\models\core\Visibility;
use common\models\Description;
use common\models\PointInTimeQuery;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Description */
/* @var $form ActiveForm */
/* @var $creatorController string */
/* @var $creatorKey string */
?>

<div class="description-form">

    <?php $form = ActiveForm::begin([
        'id' => 'description-form',
        'action' => $model->isNewRecord ?
            [
                $creatorController . '/create-description',
                'key' => $creatorKey,
            ] :
            [
                'description/update',
                'key' => $model->key,
            ],
        'method' => 'post',
    ]); ?>

    <div class="col-md-6 col-xs-12">
        <?= $form->field($model, 'code')->dropDownList(
            $model->typeNamesForThisClass(),
            ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_TYPE') . ' --- ']
        ); ?>
    </div>

    <div class="col-md-3 col-xs-6">
        <?= $form->field($model, 'lang')->dropDownList(Language::languagesLong()); ?>
    </div>

    <div class="col-md-3 col-xs-6">
        <?= $form
            ->field($model, 'visibility')
            ->dropDownList(Visibility::visibilityNames(Description::allowedVisibilities()));
        ?>
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
        <?= $form
            ->field($model, 'point_in_time_start_id')
            ->dropDownList(
                PointInTimeQuery::getListOfPointsInTimeForSelector(),
                ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_POINT_IN_TIME') . ' --- '],
            ); ?>
    </div>

    <div class="col-md-6">
        <?= $form
            ->field($model, 'point_in_time_still_valid_id')
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

    <div class="col-md-6">
        <?= $form->field($model, 'outdated')->checkbox(); ?>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
