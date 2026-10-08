<?php

use common\models\Announcement;
use yii\helpers\Html;

/* @var $model Announcement */
?>
<div data-key="<?= $model->key ?>">
    <h2>
        <?php if ($model->epic_id === null): ?>
            <span class="glyphicon glyphicon-bullhorn header-icon header-tooltip-available"
                  title="<?= Yii::t('app', 'ANNOUNCEMENT_TITLE_ALL_EPICS') ?>"
            ></span>
        <?php endif; ?>
        <?= Html::encode($model->title) ?>
        <?php if ($model->visible_from === null || time() < strtotime($model->visible_from)): ?>
            <span class="text-center unpublished-tag" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_N') ?>">
                <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_N') ?>
            </span>
        <?php elseif ($model->visible_to !== null && time() > strtotime($model->visible_to)): ?>
            <span class="text-center expired-tag" title="<?= Yii::t('app', 'TAG_TITLE_EXPIRED_N') ?>">
                <?= Yii::t('app', 'TAG_LABEL_EXPIRED_N') ?>
            </span>
        <?php endif; ?>
    </h2>

    <p class="announcement-box-time">
        <?php if ($model->visible_to !== null && time() > strtotime($model->visible_to)): ?>
            <?= $model->visible_from ?> &dash; <?= $model->visible_to ?>
        <?php else: ?>
            <?= $model->visible_from ?>
        <?php endif; ?>
    </p>

    <div>
        <?= $model->text_ready ?>
    </div>

    <hr />
</div>
