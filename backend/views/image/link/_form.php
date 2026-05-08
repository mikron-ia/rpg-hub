<?php

use common\models\core\ImageDisplayMode;
use common\models\ImageLink;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model ImageLink */
/* @var $form ActiveForm */
?>

<div class="image-form">
    <?php $form = ActiveForm::begin([
        'id' => 'story-image-link-form',
        'method' => 'post',
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'display_mode')->dropDownList(ImageDisplayMode::namesForDropdown()) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'display_weight')->textInput(['type' => 'number']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton(
                    $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
                ); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>