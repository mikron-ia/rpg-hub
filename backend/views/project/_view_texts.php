<?php

use common\models\Project;
use yii\web\View;

/* @var $this View */
/* @var $model Project */
?>

<div>
    <div class="col-md-12">
        <div>
            <?php echo $model->getLongFormattedForOperator(); ?>
        </div>
    </div>
    <div class="col-md-12 protected-notes">
        <div>
            <?php echo $model->getNotesFormatted(); ?>
        </div>
    </div>
</div>
