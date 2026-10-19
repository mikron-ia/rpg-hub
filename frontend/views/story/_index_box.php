<?php

use common\models\core\Visibility;
use common\models\Parameter;
use common\models\Story;
use yii\helpers\Html;

/** @var $model Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);
$storyTriggers = $model->getParameter(Parameter::CONTENT_WARNING);
?>

<div id="story-<?php echo $model->story_id; ?>">
    <h2>
        <?php echo Html::a(
            Html::encode((empty($storyNumberRaw) ? '' : ($storyNumberRaw . ' ')) . $model->name),
            ['view', 'key' => $model->key]
        ); ?>
        <?php if ($model->displayCodeName()): ?>
            <span class="text-center type-tag"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <?php if (!empty($storyTriggers)): ?>
            <span class="header-tooltip-available text-center type-tag"
                  title="<?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TITLE') ?>"
            >
                <?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TEXT') ?>
            </span>
        <?php endif; ?>
        <?php if ($model->story_id === $model->epic->current_story_id): ?>
            <span class="text-center type-tag"><?= Yii::t('app', 'TAG_CURRENT_F') ?></span>
        <?php endif; ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>
        <?php if ($model->getVisibility() !== Visibility::Full): ?>
            <span class="text-center unpublished-tag" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_F') ?>">
                <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_F') ?>
            </span>
        <?php endif; ?>
    </h2>

    <div class="col-md-12 text-justify">
        <?php echo $model->getShortFormatted(); ?>

        <?php if (!empty($storyTriggers)): ?>
            <hr />
            <strong><?= Yii::t('app', 'LABEL_CONTENT_WARNINGS') ?>:</strong>
            <span><?= $storyTriggers ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="clearfix"></div>
