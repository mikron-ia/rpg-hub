<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model common\models\user\UserSettingsForm */

use common\models\core\Language;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'USER_SETTINGS_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

        <div class="col-md-6"><?= $form->field($model, 'email')->textInput() ?></div>
        <div class="col-md-3"><?= $form->field($model, 'username')->textInput() ?></div>
        <div class="col-md-3"><?= $form->field($model, 'language')->dropDownList(Language::languagesLong()) ?></div>

        <div class="clearfix"></div>

        <div class="form-group pull-right">
            <?= Html::submitButton(
                Yii::t('app', 'BUTTON_SAVE'),
                ['class' => 'btn btn-primary ', 'name' => 'save-button']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
