<?php

use common\models\EpicQuery;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="character-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="col-md-6">
        <?php echo $form->field($model, 'epic_id')->widget(
            kartik\select2\Select2::className(),
            [
                'data' => EpicQuery::getListOfEpicsForSelector(),
                'options' => ['multiple' => true],
            ]
        ) ?>
    </div>

    <div class="col-md-6">
        <?php echo $form->field($model, 'name') ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
