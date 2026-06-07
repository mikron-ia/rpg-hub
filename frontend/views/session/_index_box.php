<?php

use common\models\Game;
use yii\helpers\Html;

/** @var $model Game */
?>

<div id="session-<?= $model->game_id; ?>">

    <p class="session-box session-box-closed"
       data-toggle="collapse"
       data-target="#session-notes-<?php echo $model->game_id; ?>"
       onclick="$(this).toggleClass('session-box-closed session-box-open')"
    >
        <?= Html::tag(
            'span',
            $model->getStatus()->getName(),
            [
                'class' => ['game-status', $model->getStatus()->getClass(), 'game-status-in-row'],
                'title' => $model->getStatus()->getDescription(),
            ]
        ) ?>
        <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
    </p>

    <div class="collapse" id="session-notes-<?php echo $model->game_id; ?>">
        <?= Html::tag(
            'p',
            $model->getStatus()->getName(),
            [
                'class' => ['game-status', $model->getStatus()->getClass(), 'game-status-in-description'],
                'title' => $model->getStatus()->getDescription(),
            ]
        ) ?>
        <p>
            <strong><?php echo Yii::t('app', 'LABEL_EPIC'); ?>:</strong> <?php echo Html::encode($model->epic->name); ?>
        </p>
        <?php if (!empty($model->recap)): ?>
            <p><?= '<strong>' . Yii::t('app', 'LABEL_RECAP') . ': </strong>' . $model->recap->getNameWithTime(); ?></p>
        <?php endif; ?>
        <?= $model->notesFormatted ?>
    </div>

</div>

<div class="clearfix"></div>