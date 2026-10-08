<?php

use common\models\Announcement;
use yii\helpers\Html;

/* @var $model Announcement */
?>
<div data-key="<?= $model->key ?>">
    <h4>
        <?php if ($model->epic_id): ?>
            <a href="<?= Yii::$app->urlManager->createUrl([
                'epic/view',
                'key' => $model->epic->key,
            ]) ?>"><?= Html::encode($model->epic->name) ?></a>
        <?php else: ?>
            <span class="header-tooltip-available"
                  title="<?= Yii::t('app', 'ANNOUNCEMENT_TITLE_ALL_EPICS') ?>"
            >
                <?= Yii::t('app', 'ANNOUNCEMENT_LABEL_ALL_EPICS') ?>
            </span>
        <?php endif; ?>
        /
        <?= Html::encode($model->title) ?>
    </h4>
    <p class="announcement-box-time"><?= $model->visible_from ?></p>
    <div>
        <?= $model->text_ready ?>
    </div>
</div>
