<?php

use common\models\DescriptionPack;
use yii\web\View;

/* @var $this View */
/* @var $model DescriptionPack */
?>

<ul>
    <?php foreach ($model->getTypeDescriptionsForThisClass() as $name => $description): ?>
        <li><strong><?= $name ?>:</strong> <?= $description ?></li>
    <?php endforeach; ?>
</ul>
