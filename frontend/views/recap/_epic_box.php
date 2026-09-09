<?php

use common\models\Recap;
use yii\helpers\Html;

/* @var $model Recap|null */

?>

<?php if ($model): ?>
    <div class="buttoned-header">
        <h3 title="<?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT') ?>">
            <?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?>
        </h3>
        <?php if (isset($model)): ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_RECAP_VIEW_ALL'),
                ['recap/index', 'key' => $model->epic->key],
                ['class' => 'btn btn-primary']
            ); ?>
        <?php endif; ?>
    </div>

    <div>
        <?php
        if ($model->point_in_time_id): ?>
            <p class="recap-box-time"><?= $model->pointInTime ?></p>
        <?php endif; ?>
        <div>
            <?= $model->getContentFormattedForUser(); ?>
        </div>

        <?php if (!empty($model->games)): ?>
            <p>
                <strong><?= Yii::t('app', 'LABEL_GAMES') ?>: </strong> <?= $model->getSessionNamesFormatted() ?>
            </p>
        <?php endif; ?>

    </div>
<?php endif; ?>