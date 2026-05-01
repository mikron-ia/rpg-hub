<?php

use common\models\Group;
use common\models\StoryQuery;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Group */
?>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_CONFIGURATION') ?></h3>
    <?php $form = ActiveForm::begin(['id' => 'form-group-story-assignment-public']); ?>

    <?= $form->field($model, 'groupStoryAssignmentChoicesPublic')->widget(
        Select2::class,
        [
            'data' => StoryQuery::listEpicStoriesAsArrayForDropdown(),
            'options' => ['multiple' => true, 'data-group-key' => $model->key],
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

    <?php $form = ActiveForm::begin(['id' => 'form-group-story-assignment-private']); ?>

    <?= $form->field($model, 'groupStoryAssignmentChoicesPrivate')->widget(
        Select2::class,
        [
            'data' => StoryQuery::listEpicStoriesAsArrayForDropdown(),
            'options' => ['multiple' => true, 'data-group-key' => $model->key],
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

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_STATE') ?></h3>
    <div id="group-story-assignment-list" data-group-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
