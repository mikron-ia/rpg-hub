<?php

use common\models\ParticipantRole;
use common\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Participant */

$this->title = Yii::t('app', 'TITLE_PARTICIPANT_ADD {epic}', ['epic' => $model->epic->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['view', 'id' => $model->epic_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_PARTICIPANT_ADD');
?>
<div class="participant-add">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->widget(Select2::className(), [
        'data' => User::getAllForDropdown(),
        'options' => [
            'prompt' => ' --- ' . Yii::t('app', 'PROMPT_USER') . ' --- ',
            'multiple' => false,
        ]
    ]); ?>

    <?= $form->field($model, 'roleChoices')->widget(Select2::className(), [
        'data' => ParticipantRole::roleNames(),
        'options' => [
            'multiple' => true,
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
