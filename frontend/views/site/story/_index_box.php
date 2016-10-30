<?php

use yii\helpers\Html;

/** @var $model \common\models\Story */

?>

<div id="story-<?php echo $model->story_id; ?>">

    <h4 class="center">
        <?php echo Html::a(Html::encode($model->name), ['story/view', 'id' => $model->story_id]); ?>
    </h4>

</div>

<div class="clearfix"></div>