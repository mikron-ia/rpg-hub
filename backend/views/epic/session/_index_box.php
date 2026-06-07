<?php

use common\models\Game;
use yii\helpers\Html;

/** @var $model Game */
?>
<div id="game-<?php echo $model->key; ?>">
    <p class="session-box">
        <?= Html::tag(
            'span',
            $model->getStatus()->getName(),
            [
                'class' => ['game-status', $model->getStatus()->getClass()],
                'title' => $model->getStatus()->getDescription(),
            ]
        ) ?>
        <?php echo Html::a(Html::encode($model->basics), ['game/view', 'key' => $model->key]); ?>
    </p>
</div>

<div class="clearfix"></div>