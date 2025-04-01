<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="story-<?php echo $model->game_id; ?>">

    <p class="session-box">
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
        <?php echo Html::a(Html::encode($model->basics), ['game/view', 'id' => $model->game_id]); ?>
    </p>

</div>

<div class="clearfix"></div>