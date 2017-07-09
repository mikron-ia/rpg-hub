<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="session-<?= $model->game_id; ?>">

    <p
        class="session-box session-box-closed"
        data-toggle="collapse"
        data-target="#session-notes-<?php echo $model->game_id; ?>"
        onclick="$(this).toggleClass('session-box-closed session-box-open')"
    >
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
        <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
    </p>

    <p class="collapse" id="session-notes-<?php echo $model->game_id; ?>">
        <?php if (!empty($model->notes)): ?>
            <?= $model->notes ?>
        <?php else: ?>
            <i><?= Yii::t('app', 'GAME_NOTES_MISSING') ?></i>
        <?php endif; ?>
    </p>

</div>

<div class="clearfix"></div>