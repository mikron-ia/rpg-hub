<?php

use common\models\core\Visibility;
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
    <h4 class="center">
        <?php echo Html::a(Html::encode($storyNumber . $model->name), ['story/view', 'key' => $model->key]); ?>
        <?php if ($model->visibility !== Visibility::VISIBILITY_FULL): ?>
            <span class="text-center unpublished-tag tag-smaller">
                <?= Yii::t('app', 'TAG_UNPUBLISHED_F') ?>
            </span>
        <?php endif; ?>
    </h4>
</div>

<div class="clearfix"></div>