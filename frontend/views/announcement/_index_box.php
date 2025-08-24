<?php
/* @var $model Announcement */

use common\models\Announcement;
use yii\helpers\Html;

?>
<div data-key="<?= $model->key ?>">
    <h2><?= Html::encode($model->title) ?></h2>
    <p class="announcement-box-time"><?= $model->visible_from ?></p>
    <div>
        <?= $model->text_ready ?>
    </div>
</div>
