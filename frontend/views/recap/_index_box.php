<?php

use common\models\Recap;
use yii\helpers\Html;

/** @var $model Recap */

?>

<div id="recap-<?php echo $model->recap_id; ?>">
    <h2>
        <?php echo Html::a(Html::encode($model->name), ['view', 'key' => $model->key]); ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>
    </h2>

    <div class="col-md-12 text-justify">
        <p class="recap-box-time-view">
            <?= $model->point_in_time_id ? $model->pointInTime->name : '' ?>
        </p>
        <?php echo $model->getDataFormatted(); ?>
        <?php if (!empty($model->games)): ?>
            <p>
                <strong><?= Yii::t('app', 'LABEL_GAMES') ?>: </strong>
                <?= $model->getSessionNamesFormatted() ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="clearfix"></div>