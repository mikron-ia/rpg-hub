<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="story-<?php echo $model->game_id; ?>">

    <p class="session-box" data-toggle="tooltip"  data-placement="auto left" title="<?= $model->details; ?>">
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
        <?php echo Html::a(Html::encode($model->time), ['game/view', 'id' => $model->game_id]); ?>
    </p>

</div>

<div class="clearfix"></div>