<?php

use common\models\ParticipantRole;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Participant */

$this->title = Yii::t('app', 'TITLE_PARTICIPANT_EDIT') . ': ' . $model->user->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['view', 'id' => $model->epic_id]];
$this->params['breadcrumbs'][] = $model->user->username;
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_PARTICIPANT_UPDATE');
?>
<div class="participant-edit">

    <?php $form = ActiveForm::begin([
        'id' => 'story-parameter-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'roleChoices')->checkboxList(ParticipantRole::roleNames()); ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'BUTTON_CREATE') : Yii::t('app', 'BUTTON_UPDATE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
