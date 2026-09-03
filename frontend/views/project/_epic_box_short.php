<?php

use common\models\core\Visibility;
use common\models\Project;
use yii\helpers\Html;

/** @var $model Project */
?>

<div id="project-<?= $model->project_id ?>">
    <h4 class="center">
        <?= Html::a($model->name, ['project/view', 'key' => $model->key]) ?>
        <?php if (!empty($model->displayCodeName())): ?>
            <span class="text-center type-tag tag-smaller"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <?php if ($model->getVisibility() === Visibility::GameMaster): ?>
            <span class="unpublished-tag tag-view-page" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_M') ?>">
                <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_M') ?>
            </span>
        <?php endif; ?>
        <?php if ($model->getVisibility() === Visibility::Designated): ?>
            <span class="designated-tag tag-view-page" title="<?= Yii::t('app', 'TAG_TITLE_DESIGNATED_M') ?>">
                <?= Yii::t('app', 'TAG_LABEL_DESIGNATED_M') ?>
            </span>
        <?php endif; ?>
    </h4>
</div>

<div class="clearfix"></div>
