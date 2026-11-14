<?php

use common\models\Parameter;
use common\models\Story;
use yii\helpers\Html;

/** @var $model Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);
$storyNumber = $storyNumberRaw ? $storyNumberRaw . ' ' : '';

?>

<div id="story-<?php echo $model->story_id; ?>">
    <h4>
        <?php echo Html::a(Html::encode($storyNumber . $model->name), ['story/view', 'key' => $model->key]); ?>
        <?php if ($model->story_id === $model->epic->current_story_id): ?>
            <span class="tag-current"><?= Yii::t('app', 'TAG_CURRENT_F') ?></span>
        <?php endif; ?>
    </h4>
</div>

<div class="clearfix"></div>
