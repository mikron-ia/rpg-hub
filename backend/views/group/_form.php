<?php

use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use common\models\EpicQuery;
use common\models\Group;
use common\models\GroupQuery;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $form yii\widgets\ActiveForm */
/* @var $epicListForSelector string[] */
?>

<div class="group-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'epic_id')->widget(
            Select2::class,
            ['data' => EpicQuery::getListOfEpicsForSelector()]
        ); ?>
    </div>

    <div class="col-md-4">
        <?= $form
            ->field($model, 'visibility')
            ->dropDownList(Visibility::visibilityNames(Group::allowedVisibilities())
            ) ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'master_group_id')->widget(
            Select2::class,
            [
                'data' => GroupQuery::getAllFromCurrentEpicForSelector(),
                'options' => ['placeholder' => ' --- ' . Yii::t('app', 'MASTER_GROUP_PROMPT') . ' --- '],
                'pluginOptions' => ['allowClear' => true],
            ]
        ); ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'importance_category')->dropDownList(ImportanceCategory::importanceNames()) ?>
    </div>

    <div class="col-md-9">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'display_as_tab')->checkbox() ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
        <div class="col-md-12">
            <?= $form->field($model, 'is_off_the_record_change')->checkbox() ?>
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
