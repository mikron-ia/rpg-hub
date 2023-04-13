<?php

use common\models\Game;
use yii\helpers\Html;

/** @var $model Game */
?>

<div id="session-<?= $model->game_id; ?>">
    <?php if (!empty($model->notes) || !empty($model->recap)): ?>
        <p class="session-box session-box-closed"
           data-toggle="collapse"
           data-target="#session-notes-<?php echo $model->game_id; ?>"
           onclick="$(this).toggleClass('session-box-closed session-box-open')"
        >
            <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
            <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
        </p>

        <div class="collapse" id="session-notes-<?php echo $model->game_id; ?>">
            <?php if (!empty($model->recap)): ?>
                <p>
                    <?= '<strong>' . Yii::t('app', 'LABEL_RECAP') . ': </strong>' . $model->recap->getNameWithTime(); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($model->notes)): ?>
                <?= $model->notesFormatted ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="session-box">
            <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
            <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
        </p>
    <?php endif; ?>
</div>

<div class="clearfix"></div>