<?php

use common\models\Game;
use yii\helpers\Html;

/** @var $model Game */
?>

<div id="session-<?= $model->game_id; ?>">
    <?php if (!empty($model->notes) || !empty($model->recap)): ?>
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
            <?php if (!empty($model->recap)): ?>
                <p>
                    <strong><?= Yii::t('app', 'LABEL_RECAP') ?>: </strong>
                    <?= Html::a($model->recap->getNameWithTime(), ['recap/view', 'key' => $model->recap->key]); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($model->notes)): ?>
                <?= $model->notesFormatted ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="session-box session-box-widening">
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
        </div>
    <?php endif; ?>
</div>

<div class="clearfix"></div>