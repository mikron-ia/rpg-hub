<?php

use common\models\Announcement;
use yii\helpers\Html;

/** @var $model Announcement */

?>

<div id="announcement-<?php echo $model->announcement_id; ?>">
    <h4 class="announcement-box">
        <?php if($model->epic_id === null):?>
            <span class="glyphicon glyphicon-bullhorn header-icon header-tooltip-available"
                  title="<?= Yii::t('app', 'ANNOUNCEMENT_TITLE_ALL_EPICS') ?>"
            ></span>
            <?= Html::encode($model->title) ?>
        <?php else: ?>
            <?= Html::a(Html::encode($model->title), ['announcement/view', 'key' => $model->key]); ?>
        <?php endif;?>
    </h4>

    <p class="announcement-box-time">
        <?php if (isset($model->visible_to)): ?>
            <?= Yii::t(
                'app',
                'ANNOUNCEMENT_TIME_FORMAT_FROM_TO {from} {to}',
                ['from' => $model->visible_from, 'to' => $model->visible_to]
            ) ?>
        <?php elseif (isset($model->visible_from)): ?>
            <?= Yii::t(
                'app',
                'ANNOUNCEMENT_TIME_FORMAT_FROM {from}}',
                ['from' => $model->visible_from]
            ) ?>
        <?php else: ?>
            <?= Yii::t('app', 'ANNOUNCEMENT_TIME_FORMAT_INVISIBLE') ?>
        <?php endif; ?>
    </p>
    <div>
        <?= $model->text_ready ?>
    </div>
</div>

<div class="clearfix"></div>
