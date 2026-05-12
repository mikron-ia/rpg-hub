<?php

use common\models\core\Visibility;
use common\models\Epic;
use common\models\Group;
use common\models\GroupQuery;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model GroupQuery */
/* @var $form ActiveForm */
/* @var $epic Epic */
/* @var $actionUrl string */

$actionUrl = $actionUrl ?? 'index';

?>

<div class="group-search">
    <?php $form = ActiveForm::begin([
        'action' => [$actionUrl, 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?php echo $form->field($model, 'visibility')->widget(
        class: Select2::class,
        config: [
            'data' => Visibility::visibilityNames(Group::allowedVisibilities()),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
