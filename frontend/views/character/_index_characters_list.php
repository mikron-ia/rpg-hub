<?php

use common\models\Character;
use yii\data\DataProviderInterface;
use yii\widgets\ListView;

/* @var $model Character */
/* @var $dataProvider DataProviderInterface */

?>

<div id="characters">
    <?= $this->render('../_common/alpha') ?>
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="info-box">' . Yii::t('app', 'PROJECTS_NOT_FOUND') . '</p>',
        'layout' => '<div class="object-list-box">{summary}  <ul class="object-list-wall">{items}</ul> {pager}</div>',
        'itemOptions' => ['class' => 'item object-list-brick', 'tag' => 'li'],
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
