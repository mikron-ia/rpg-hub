<?php

use common\models\core\Language;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'language')->dropDownList(Language::languagesLong()) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'user_role')->dropDownList(
            User::allowedUserRoleNames(),
            ['disabled' => ($model->getUserRoleCode() === User::USER_ROLE_ADMINISTRATOR)]
        ) ?>
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
