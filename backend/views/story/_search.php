<?php

use common\models\core\Visibility;
use common\models\Epic;
use common\models\Story;
use common\models\StoryQuery;
use common\models\type\StoryType;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model StoryQuery */
/* @var $form ActiveForm */
/* @var $epic Epic */
?>

<div class="story-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index', 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'descriptions') ?>

    <?= $form->field($model, 'parameters') ?>

    <?= $form->field($model, 'code')->widget(
        Select2::class,
        [
            'data' => StoryType::namesForDropdown(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <?php echo $form->field($model, 'visibility')->widget(
        kartik\select2\Select2::class,
        [
            'data' => Visibility::visibilityNames(Story::allowedVisibilities()),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
