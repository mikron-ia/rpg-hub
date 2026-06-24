<?php

use common\models\Project;
use yii\web\View;

/* @var $this View */
/* @var $model Project */
/* @var $showPrivates bool */
?>

<div class="col-lg-12">
    <?= $showPrivates ? $model->getLongFormattedForOperator() : $model->getLongFormattedForUser(); ?>
</div>
