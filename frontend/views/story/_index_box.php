<?php

use common\models\Parameter;
use common\models\Story;
use yii\helpers\Html;

/** @var $model Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);
$storyTriggers = $model->getParameter(Parameter::CONTENT_WARNING);
?>

<div id="story-<?= $model->story_id ?>">
    <h4>
        <?= Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key]) ?> / <?= Html::a(
            Html::encode((empty($storyNumberRaw) ? '' : $storyNumberRaw . ' ') . $model->name),
            ['story/view', 'key' => $model->key]
        ); ?>
        <?php if ($model->displayCodeName()): ?>
            <span class="text-center type-tag tag-smaller"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <?php if (!empty($storyTriggers)): ?>
            <span class="header-tooltip-available text-center type-tag tag-smaller"
                  title="<?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TITLE') ?>"
            >
                <?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TEXT') ?>
            </span>
        <?php endif; ?>
    </h4>
    <div class="col-md-12 text-justify">
        <?= $model->getShortFormatted() ?>

        <?php if (!empty($storyTriggers)): ?>
            <strong><?= Yii::t('app', 'LABEL_CONTENT_WARNINGS') ?>:</strong>
            <span><?= $storyTriggers ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="clearfix"></div>
