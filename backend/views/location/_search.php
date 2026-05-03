<?php

use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\Location;
use common\models\LocationQuery;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model LocationQuery */
/* @var $form ActiveForm */
/* @var $epic Epic */
/* @var $actionUrl string */

$actionUrl = $actionUrl ?? 'index';

?>

<div class="location-search">
    <?php $form = ActiveForm::begin([
        'action' => [$actionUrl, 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?php echo $form->field($model, 'tagline') ?>

    <?php echo $form->field($model, 'visibility')->widget(
        kartik\select2\Select2::class,
        [
            'data' => Visibility::visibilityNames(Location::allowedVisibilities()),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <?php echo $form->field($model, 'importance_category')->widget(
        kartik\select2\Select2::class,
        [
            'data' => ImportanceCategory::importanceNames(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <div class="form-location">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
