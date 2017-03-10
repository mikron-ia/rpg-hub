<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="story-<?= $model->game_id; ?>">

    <p class="session-box">
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
        <?php echo Html::tag('span', Html::encode($model->basics), []); ?>
    </p>

</div>

<div class="clearfix"></div>