<?php

use common\models\core\Visibility;
use common\models\Parameter;
use common\models\Project;
use yii\helpers\Html;

/** @var $model Project */

$contentWarning = $model->getParameter(Parameter::CONTENT_WARNING);
?>

<div id="project-<?php echo $model->project_id; ?>">
    <h2>
        <?php echo Html::a(Html::encode($model->name), ['view', 'key' => $model->key]); ?>
        <?php if ($model->displayCodeName()): ?>
            <span class="text-center type-tag"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <?php if (!empty($contentWarning)): ?>
            <span class="header-tooltip-available text-center type-tag"
                  title="<?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TITLE') ?>"
            >
                <?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TEXT') ?>
            </span>
        <?php endif; ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>

        <?php if ($model->getVisibility() === Visibility::GameMaster): ?>
            <span class="text-center unpublished-tag" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_M') ?>">
                <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_M') ?>
            </span>
        <?php endif; ?>
        <?php if ($model->getVisibility() === Visibility::Designated): ?>
            <span class="text-center designated-tag" title="<?= Yii::t('app', 'TAG_TITLE_DESIGNATED_M') ?>">
                <?= Yii::t('app', 'TAG_LABEL_DESIGNATED_M') ?>
            </span>
        <?php endif; ?>
    </h2>

    <div class="col-md-12 text-justify">
        <?php echo $model->getShortFormatted(); ?>

        <?php if (!empty($contentWarning)): ?>
            <hr />
            <strong><?= Yii::t('app', 'LABEL_CONTENT_WARNINGS') ?>:</strong>
            <span><?= $contentWarning ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="clearfix"></div>
