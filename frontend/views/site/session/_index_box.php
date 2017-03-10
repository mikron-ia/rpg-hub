<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="story-<?= $model->game_id; ?>">

    <p class="session-box" data-toggle="tooltip"  data-placement="auto left" title="<?= $model->details; ?>">
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
        <?php echo Html::tag('span', Html::encode($model->time), []); ?>
    </p>

</div>

<div class="clearfix"></div>