<?php

use common\models\user\PasswordChange;
use kartik\password\PasswordInput;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model PasswordChange */

$this->title = Yii::t('app', 'PASSWORD_CHANGE_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-password-change">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'password-change-form']); ?>

            <?= $form->field($model, 'password_old')->passwordInput() ?>
            <?= $form->field($model, 'password_new')->widget(
                PasswordInput::class,
                [
                    'pluginOptions' => [
                        'showMeter' => true,
                        'toggleMask' => false,
                    ]
                ]
            ); ?>
            <?= $form->field($model, 'password_again')->passwordInput() ?>

            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('app', 'BUTTON_SAVE'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
