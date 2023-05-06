<?php

use common\models\ParticipantRole;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Participant */

$this->title = Yii::t('app', 'TITLE_PARTICIPANT_EDIT {epic}', ['epic' => $model->epic->name]) .
    ': ' . $model->user->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = $model->user->username;
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_PARTICIPANT_UPDATE');
?>
<div class="participant-edit">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'story-parameter-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'roleChoices')->widget(Select2::class, [
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
