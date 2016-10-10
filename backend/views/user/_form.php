<?php

use common\models\core\Language;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'language')->dropDownList(Language::languagesLong()) ?>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <?php echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        );
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
