<?php

use common\models\core\Visibility;
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
        <?php if ($model->getVisibility() !== Visibility::Full): ?>
            <span class="text-center unpublished-tag" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_N') ?>">
                <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_N') ?>
            </span>
        <?php endif; ?>
    </h2>

    <div class="col-md-12 text-justify">
        <p class="recap-box-time-view">
            <?= $model->point_in_time_id ? $model->pointInTime->name : '' ?>
        </p>
        <?= $model->getContentFormattedForUser(); ?>
        <?php if (!empty($model->games)): ?>
            <p>
                <strong><?= Yii::t('app', 'LABEL_GAMES') ?>: </strong>
                <?= $model->getSessionNamesFormatted() ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="clearfix"></div>