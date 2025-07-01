<?php

use common\models\Announcement;
use yii\helpers\Html;

/** @var $model Announcement */

?>

<div id="announcement-<?php echo $model->announcement_id; ?>">

    <p class="announcement-box">
        <?php echo Html::a(Html::encode($model->title), ['announcement/view', 'key' => $model->key]); ?>
    </p>

</div>

<div class="clearfix"></div>
