<?php

use common\models\Story;
use yii\web\View;

/* @var $this View */
/* @var $model Story */
/* @var $showPrivates bool */
?>

<div class="col-lg-12">
    <?= $model->getLongFormatted(); ?>
</div>
