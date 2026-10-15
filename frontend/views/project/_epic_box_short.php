<?php

use common\models\core\Visibility;
use common\models\Parameter;
use common\models\Project;
use yii\helpers\Html;

/** @var $model Project */

$contentWarning = $model->getParameter(Parameter::CONTENT_WARNING);
?>

<div id="project-<?= $model->project_id ?>">
    <h4 class="center">
        <?= Html::a($model->name, ['project/view', 'key' => $model->key]) ?>
        <?php if (!empty($model->displayCodeName())): ?>
            <span class="text-center type-tag tag-smaller"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <?php if (!empty($contentWarning)): ?>
            <span class="header-tooltip-available text-center type-tag tag-smaller"
                  title="<?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TITLE') ?>"
            >
                <?= Yii::t('app', 'STORY_TAG_HAS_CONTENT_WARNING_TEXT') ?>
            </span>
        <?php endif; ?>
        <?php if ($model->getVisibility() === Visibility::GameMaster): ?>
            <span class="tag-smaller unpublished-tag" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_M') ?>">
                <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_M') ?>
            </span>
        <?php endif; ?>
        <?php if ($model->getVisibility() === Visibility::Designated): ?>
            <span class="tag-smaller designated-tag" title="<?= Yii::t('app', 'TAG_TITLE_DESIGNATED_M') ?>">
                <?= Yii::t('app', 'TAG_LABEL_DESIGNATED_M') ?>
            </span>
        <?php endif; ?>
    </h4>
</div>

<div class="clearfix"></div>
