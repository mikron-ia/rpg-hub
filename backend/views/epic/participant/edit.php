<?php

use common\models\ParticipantRole;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Participant */

?>
<div class="participant-edit">

    <?php $form = ActiveForm::begin([
        'id' => 'story-parameter-form',
        'action' =>
            [
                'epic/participant-edit',
                'participant_id' => $model->participant_id
            ],
        'method' => 'post',
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
