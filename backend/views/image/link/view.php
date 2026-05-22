<?php

use common\dto\ImageDisplayObject;
use common\models\ImageLink;
use yii\web\View;

/* @var $this View */
/* @var $model ImageLink */

$imageToDisplay = new ImageDisplayObject(
    url: $model->link,
    alt: $model->image->alt,
    title: $model->image->title,
    height: $model->image->display_height,
    width: $model->image->display_width,
);
?>
<div class="image-link-view">
    <?= $imageToDisplay ?>
</div>
