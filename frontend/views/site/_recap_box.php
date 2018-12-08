<?php
/* @var $model \common\models\Recap */

use yii\helpers\Html;

?>

<div id="session-<?= $model->recap_id; ?>">
    <p class="recap-box-time">
        <?= Html::a(
            $model->epic->name,
            ['epic/view', 'key' => $model->epic->key]
        ) . ($model->point_in_time_id ? ' / ' . $model->pointInTime->name : '') ?>
    </p>
    <?= $model->getDataFormatted(); ?>
</div>
