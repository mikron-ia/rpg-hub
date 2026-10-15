<?php

use common\models\Parameter;
use common\models\Project;
use yii\helpers\Html;

/** @var $model Project */

$contentWarning = $model->getParameter(Parameter::CONTENT_WARNING);
?>

<div id="project-<?= $model->project_id ?>">
    <h4>
        <?= Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key]) ?> / <?= Html::a(
            Html::encode($model->name),
            ['project/view', 'key' => $model->key]
        ); ?>
        <?php if ($model->displayCodeName()): ?>
            <span class="text-center type-tag tag-smaller"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <?php if (!empty($contentWarning)): ?>
            <span class="header-tooltip-available text-center type-tag tag-smaller"
                  title="<?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TITLE') ?>"
            >
                <?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TEXT') ?>
            </span>
        <?php endif; ?>
    </h4>
    <div class="col-md-12 text-justify">
        <?= $model->getShortFormatted() ?>

        <?php if (!empty($contentWarning)): ?>
            <strong><?= Yii::t('app', 'LABEL_CONTENT_WARNINGS') ?>:</strong>
            <span><?= $contentWarning ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="clearfix"></div>
