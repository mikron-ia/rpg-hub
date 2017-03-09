<?php

use yii\helpers\Html;

/** @var $model \common\models\Game */

?>

<div id="story-<?php echo $model->game_id; ?>">

    <h4 class="center">
        <?php echo Html::a(Html::encode($model->time), ['game/view', 'id' => $model->game_id]); ?>
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
    </h4>

</div>

<div class="clearfix"></div>