<?php

use common\models\GroupQuery;
use common\models\Story;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Story */
?>

<div class="col-md-6">
    <?php $form = ActiveForm::begin(['id' => 'form-story-group-assignment-public']); ?>

    <?= $form->field($model, 'storyGroupAssignmentChoicesPublic')->widget(
        Select2::class,
        [
            'data' => GroupQuery::listEpicGroupsAsArray(),
            'options' => ['multiple' => true, 'data-story-key' => $model->key],
            'pluginOptions' => ['allowClear' => true],
        ],
    ); ?>

    <div class="form-group text-right">
        <?= Html::submitButton(
            Yii::t('app', 'BUTTON_SAVE'),
            ['class' => 'btn btn-primary']
        );
        ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php $form = ActiveForm::begin(['id' => 'form-story-group-assignment-private']); ?>

    <?= $form->field($model, 'storyGroupAssignmentChoicesPrivate')->widget(
        Select2::class,
        [
            'data' => GroupQuery::listEpicGroupsAsArray(),
            'options' => ['multiple' => true, 'data-story-key' => $model->key],
            'pluginOptions' => ['allowClear' => true],
        ],
    ); ?>

    <div class="form-group text-right">
        <?= Html::submitButton(
            Yii::t('app', 'BUTTON_SAVE'),
            ['class' => 'btn btn-primary']
        );
        ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div class="col-md-6" id="story-group-assignment-list" data-story-key="<?= $model->key ?>">
    <div class="circle-loader"></div>
</div>
