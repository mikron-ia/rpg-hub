<?php

use common\models\Epic;
use common\models\Scenario;
use common\models\ScenarioQuery;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model ScenarioQuery */
/* @var $form ActiveForm */
/* @var $epic Epic */
?>

<div class="scenario-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index', 'epic' => $epic->key],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'tag_line') ?>

    <?php echo $form->field($model, 'status')->widget(
        kartik\select2\Select2::class,
        [
            'data' => Scenario::statusNames(),
            'options' => ['multiple' => true],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
