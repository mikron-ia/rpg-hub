<?php

use common\models\Game;
use yii\helpers\Html;

/** @var $model Game */
?>
<div id="game-<?php echo $model->key; ?>">
    <p class="session-box">
        <?= Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]) ?>
        <?php echo Html::a(Html::encode($model->basics), ['game/view', 'key' => $model->key]); ?>
    </p>
</div>

<div class="clearfix"></div>