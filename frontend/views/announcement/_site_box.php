<?php
/* @var $model Announcement */

use common\models\Announcement;
use yii\helpers\Html;

?>
<div data-key="<?= $model->key ?>">
    <h4>
        <?php if ($model->epic_id): ?>
            <a href="<?= Yii::$app->urlManager->createUrl([
                'epic/view',
                'key' => $model->epic->key
            ]) ?>"><?= Html::encode($model->epic->name) ?></a> /
        <?php endif; ?>
        <?= Html::encode($model->title) ?>
    </h4>
    <p class="announcement-box-time"><?= $model->visible_from ?></p>
    <div>
        <?= $model->text_ready ?>
    </div>
</div>
