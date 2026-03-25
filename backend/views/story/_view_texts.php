<?php

use common\models\Story;
use yii\web\View;

/* @var $this View */
/* @var $model Story */

?>

<div>
    <div class="col-md-12">
        <div>
            <?php echo $model->getLongFormatted(); ?>
        </div>
    </div>
</div>
