<?php

use common\models\Character;
use yii\data\DataProviderInterface;
use yii\widgets\ListView;

/* @var $model Character */
/* @var $dataProvider DataProviderInterface */

?>

<div id="characters">
    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="info-box">' . Yii::t('app', 'PROJECTS_NOT_FOUND') . '</p>',
        'options' => ['tag' => 'ul', 'class' => 'character-list'],
        'itemOptions' => ['class' => 'item', 'tag' => 'li'],
        'itemView' => function (Character $model, $key, $index, $widget) {
            $tags = sprintf(
                '<span class="%s">%s</span>',
                'text-center seen-tag-common ' . $model->showSightingCSS() . ' seen-tag-line',
                $model->showSightingStatus()
            );
            return sprintf('%s (%s) %s', $model, $model->tagline, $tags);
        },
    ]) ?>
</div>
