<?php
/* @var $model Announcement */

use common\models\Announcement;
use yii\helpers\Html;

?>
<div data-key="<?= $model->key ?>">
    <h4>
        <?php if($model->epic_id === null):?>
            <span class="glyphicon glyphicon-bullhorn header-icon header-tooltip-available"
                  title="<?= Yii::t('app', 'ANNOUNCEMENT_TITLE_ALL_EPICS') ?>"
            ></span>
        <?php endif;?>
        <?= Html::encode($model->title) ?>
    </h4>
    <p class="announcement-box-time"><?= $model->visible_from ?></p>
    <div>
        <?= $model->text_ready ?>
    </div>
</div>
