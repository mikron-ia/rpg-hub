<?php

use common\models\AnnouncementQuery;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/** @var View $this */
/** @var AnnouncementQuery $model */
/** @var ActiveForm $form */
?>

<div class="announcement-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'text_ready') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
