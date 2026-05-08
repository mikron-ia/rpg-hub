<?php

use common\models\ImageLink;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model ImageLink */

$imageStyleComponents = [];

if ($model->image->display_width !== null) {
    $imageStyleComponents[] = sprintf('width: %spx;', $model->image->display_width);
}

if ($model->image->display_height !== null) {
    $imageStyleComponents[] = sprintf('height: %spx;', $model->image->display_height);
}
$imageStyle = implode(' ', $imageStyleComponents);
?>
<div class="image-link-view">
    <?= Html::img(
        $model->link,
        [
            'alt' => $model->image->alt,
            'class' => 'img-responsive img-link-view-img-in-modal',
            'style' => $imageStyle,
            'title' => $model->image->title,
        ]
    ) ?>
</div>
