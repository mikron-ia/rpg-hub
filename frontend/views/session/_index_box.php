<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="session-<?= $model->game_id; ?>">

    <p class="session-box session-box-closed"
       data-toggle="collapse"
       data-target="#session-notes-<?php echo $model->game_id; ?>"
       onclick="$(this).toggleClass('session-box-closed session-box-open')"
    >
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass(), 'game-status-in-row']]) ?>
        <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
    </p>

    <div class="collapse" id="session-notes-<?php echo $model->game_id; ?>">
        <?= Html::tag('p', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass(), 'game-status-in-description']]) ?>
        <p>
            <strong><?php echo Yii::t('app', 'LABEL_EPIC'); ?>:</strong> <?php echo Html::encode($model->epic->name); ?>
        </p>
        <?= $model->notesFormatted ?>
    </div>

</div>

<div class="clearfix"></div>