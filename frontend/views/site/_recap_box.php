<?php

/* @var $model \common\models\Recap */

?>

<div id="session-<?= $model->recap_id; ?>">
    <p class="recap-box-time"><?= $model->epic->name . ($model->point_in_time_id ? ' / '.$model->pointInTime->name : '') ?></p>
    <?= $model->getDataFormatted(); ?>
</div>
