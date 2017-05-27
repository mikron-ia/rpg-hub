<?php

use yii\helpers\Html;

/** @var $model \common\models\Article */
?>

<div id="article-<?php echo $model->article_id; ?>">

    <h2>
        <?php echo Html::a(Html::encode($model->title), ['view', 'key' => $model->key]); ?>
        <span class="text-center <?= $model->showSightingCSS() ?> seen-tag-header">
            <?= $model->showSightingStatus() ?>
        </span>
    </h2>

    <p class="subtitle"><?= $model->subtitle ?></p>

</div>

<div class="clearfix"></div>