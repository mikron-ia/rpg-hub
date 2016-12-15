<?php

use yii\helpers\Html;

/** @var $model \common\models\Story */

$storyNumberRaw = $model->getParameter(\common\models\Parameter::STORY_NUMBER);

if ($storyNumberRaw) {
    $storyNumber = $storyNumberRaw . ' ';
} else {
    $storyNumber = '';
}

?>

<div id="story-<?php echo $model->story_id; ?>">

    <h2 class="center">
        <?php echo Html::a(Html::encode($storyNumber . $model->name), ['view', 'id' => $model->story_id]); ?>
    </h2>

    <div class="col-md-12 text-justify">
        <?php echo $model->short; ?>
    </div>

</div>

<div class="clearfix"></div>