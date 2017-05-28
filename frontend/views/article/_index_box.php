<?php

use common\models\core\Visibility;
use yii\helpers\Html;

/** @var $model \common\models\Article */
?>

<div id="article-<?php echo $model->article_id; ?>">

    <h2>
        <?php echo Html::a(Html::encode($model->title), ['view', 'key' => $model->key]); ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>
        <?php if ($model->visibility !== Visibility::VISIBILITY_FULL): ?>
            <span class="text-center unpublished-tag">
                <?= Yii::t('app', 'TAG_UNPUBLISHED_M') ?>
            </span>
        <?php endif; ?>
    </h2>

    <p class="subtitle"><?= $model->subtitle ?></p>

</div>

<div class="clearfix"></div>