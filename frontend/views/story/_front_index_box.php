<?php

use common\models\Parameter;
use yii\helpers\Html;

/** @var $model \common\models\Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);

if ($storyNumberRaw) {
    $storyNumber = $storyNumberRaw . ' ';
} else {
    $storyNumber = '';
}

?>

<div id="story-<?php echo $model->story_id; ?>">
    <h4>
        <?= $model->epic->name ?> / <?= Html::a(
            Html::encode($storyNumber . $model->name),
            ['view', 'key' => $model->key]
        ); ?>
    </h4>
    <div class="col-md-12 text-justify">
        <?php echo $model->getShortFormatted(); ?>
    </div>
</div>

<div class="clearfix"></div>