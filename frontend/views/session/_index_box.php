<?php

use common\models\Game;
use yii\helpers\Html;

/** @var $model Game */
?>

<div id="session-<?= $model->game_id; ?>">

    <div class="session-box session-box-widening session-box-closed"
         data-toggle="collapse"
         data-target="#session-notes-<?php echo $model->game_id; ?>"
         onclick="$(this).toggleClass('session-box-closed session-box-opened')"
    >
        <div class="session-box-status">
            <?= Html::tag(
                'span',
                $model->getStatus()->getName(),
                [
                    'class' => ['game-status', $model->getStatus()->getClass(), 'game-status-widening'],
                    'title' => $model->getStatus()->getDescription(),
                ]
            ) ?>
        </div>
        <div class="session-box-title">
            <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
        </div>
        <div class="session-box-toggle" aria-hidden="true">
            <span class="session-box-mark session-box-mark-opened">-</span>
            <span class="session-box-mark session-box-mark-closed">+</span>
        </div>
    </div>

    <div class="collapse session-box-notes" id="session-notes-<?php echo $model->game_id; ?>">
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