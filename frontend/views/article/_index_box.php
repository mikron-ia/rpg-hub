<?php

use common\models\Article;
use common\models\core\Visibility;
use yii\helpers\Html;

/** @var $model Article */
?>

<div id="article-<?php echo $model->article_id; ?>">

    <h2>
        <?php echo Html::a(Html::encode($model->title), ['view', 'key' => $model->key]); ?>
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

    <p class="subtitle"><?= $model->subtitle ?></p>

    <div class="col-md-12 text-justify">
        <?= $model->outline_ready ?>
    </div>

</div>

<div class="clearfix"></div>