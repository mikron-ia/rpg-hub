<?php

use common\models\Recap;
use yii\helpers\Html;

/* @var $model Recap */

?>

<div id="recap-<?= $model->recap_id; ?>">
    <p class="recap-box-time">
        <?= Html::a(
            $model->epic->name,
            ['epic/view', 'key' => $model->epic->key]
        ) . ($model->point_in_time_id ? ' / ' . $model->pointInTime->name : '') ?>
    </p>
    <?= $model->getContentFormatted(); ?>
    <?php if (!empty($model->games)): ?>
        <p>
            <strong><?= Yii::t('app', 'LABEL_GAMES') ?>: </strong>
            <?= $model->getSessionNamesFormatted() ?>
        </p>
    <?php endif; ?>
</div>
