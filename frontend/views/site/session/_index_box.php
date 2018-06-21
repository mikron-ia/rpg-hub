<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="session-<?= $model->game_id; ?>">

    <?php if (!empty($model->notes)): ?>
        <p
                class="session-box session-box-closed"
                data-toggle="collapse"
                data-target="#session-notes-<?php echo $model->game_id; ?>"
                onclick="$(this).toggleClass('session-box-closed session-box-open')"
        >
            <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
            <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
        </p>

        <div class="collapse" id="session-notes-<?php echo $model->game_id; ?>">
            <?= $model->notesFormatted ?>
        </div>
    <?php else: ?>
        <p class="session-box">
            <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
            <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
        </p>
    <?php endif; ?>

</div>

<div class="clearfix"></div>