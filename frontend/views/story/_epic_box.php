<?php

use common\models\core\Visibility;
use common\models\Parameter;
use common\models\Story;
use yii\helpers\Html;

/** @var $model Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);

if ($storyNumberRaw) {
    $storyNumber = $storyNumberRaw . ' ';
} else {
    $storyNumber = '';
}

?>

<div id="story-<?php echo $model->story_id; ?>">
    <h2>
        <?php echo Html::a(Html::encode($storyNumber . $model->name), ['view', 'key' => $model->key]); ?>
        <?php if (!empty($model->hasCodeName())): ?>
            <span class="text-center type-tag"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>
        <?php if ($model->getVisibility() !== Visibility::VISIBILITY_FULL): ?>
            <span class="text-center unpublished-tag">
                <?= Yii::t('app', 'TAG_UNPUBLISHED_F') ?>
            </span>
        <?php endif; ?>
    </h2>

    <div class="col-md-12 text-justify">
        <?php echo $model->getShortFormatted(); ?>
    </div>
</div>

<div class="clearfix"></div>
