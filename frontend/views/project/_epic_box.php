<?php

use common\models\core\Visibility;
use common\models\Project;
use yii\helpers\Html;

/** @var $model Project */
?>

<div id="project-<?php echo $model->project_id; ?>">
    <h2>
        <?php echo Html::a(Html::encode($model->name), ['view', 'key' => $model->key]); ?>
        <?php if ($model->displayCodeName()): ?>
            <span class="text-center type-tag"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>
        <?php if ($model->getVisibility() !== Visibility::Full): ?>
            <span class="text-center unpublished-tag" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_M') ?>">
                <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_F') ?>
            </span>
        <?php endif; ?>
    </h2>

    <div class="col-md-12 text-justify">
        <?php echo $model->getShortFormatted(); ?>
    </div>
</div>

<div class="clearfix"></div>
