<?php

use common\models\Character;
use common\models\StoryQuery;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Character */
?>

<div class="col-md-6">
    <?php $form = ActiveForm::begin(['id' => 'form-character-story-assignment-public']); ?>

    <?= $form->field($model, 'characterStoryAssignmentChoicesPublic')->widget(
        Select2::class,
        [
            'data' => StoryQuery::listEpicStoriesAsArrayForDropdown(),
            'options' => ['multiple' => true, 'data-character-key' => $model->key],
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

    <?php $form = ActiveForm::begin(['id' => 'form-character-story-assignment-private']); ?>

    <?= $form->field($model, 'characterStoryAssignmentChoicesPrivate')->widget(
        Select2::class,
        [
            'data' => StoryQuery::listEpicStoriesAsArrayForDropdown(),
            'options' => ['multiple' => true, 'data-character-key' => $model->key],
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

<div class="col-md-6" id="character-story-assignment-list" data-character-key="<?= $model->key ?>">
    <div class="circle-loader"></div>
</div>
