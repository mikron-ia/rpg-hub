<?php

use common\models\UserEpic;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserEpic */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="participant-form">

    <?php $form = ActiveForm::begin([
        'id' => 'story-parameter-form',
        'action' =>
            [
                'epic/participant-add',
                'epic_id' => $model->epic_id
            ],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'user_id')->dropDownList(\common\models\User::getAllForDropdown()); ?>

    <?= $form->field($model, 'role')->dropDownList(
        UserEpic::roleNames(),
        ['prompt' => ' --- ' . Yii::t('app', 'PROMPT_SELECT_TYPE') . ' --- ']
    ); ?>


    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
