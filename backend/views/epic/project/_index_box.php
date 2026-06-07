<?php

use common\models\Parameter;
use common\models\Project;
use common\models\Story;
use yii\helpers\Html;

/** @var $model Project */
?>

<div id="story-<?php echo $model->project_id; ?>">
    <h4 class="center">
        <?php echo Html::a(Html::encode($model->name), ['project/view', 'key' => $model->key]); ?>
    </h4>
</div>

<div class="clearfix"></div>