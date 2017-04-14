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

    <h2>
        <?php echo Html::a(Html::encode($storyNumber . $model->name), ['view', 'key' => $model->key]); ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>
    </h2>

    <div class="col-md-12 text-justify">
        <?php echo $model->getShortFormatted(); ?>
    </div>

</div>

<div class="clearfix"></div>