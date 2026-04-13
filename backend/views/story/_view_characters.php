<?php

use common\models\CharacterQuery;
use common\models\Story;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Story */
?>

<div class="col-md-6">
    <?php $form = ActiveForm::begin(['id' => 'form-story-character-assignment-public']); ?>

    <?= $form->field($model, 'storyCharacterAssignmentChoicesPublic')->widget(
        Select2::class,
        [
            'data' => CharacterQuery::listEpicCharactersAsArray(),
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

    <?php $form = ActiveForm::begin(['id' => 'form-story-character-assignment-private']); ?>

    <?= $form->field($model, 'storyCharacterAssignmentChoicesPrivate')->widget(
        Select2::class,
        [
            'data' => CharacterQuery::listEpicCharactersAsArray(),
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

<div class="col-md-6" id="story-character-assignment-list" data-story-key="<?= $model->key ?>">
    <div class="circle-loader"></div>
</div>
