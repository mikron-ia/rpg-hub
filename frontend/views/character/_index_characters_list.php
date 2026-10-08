<?php

use common\models\Character;
use yii\data\DataProviderInterface;
use yii\widgets\ListView;

/* @var $model Character */
/* @var $dataProvider DataProviderInterface */
?>

<div id="characters">
    <?= $this->render('../_common/beta') ?>
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="info-box">' . Yii::t('app', 'PROJECTS_NOT_FOUND') . '</p>',
        'layout' => '<div class="object-list-box">{summary}  <ul class="object-list-wall">{items}</ul> {pager}</div>',
        'itemView' => function (Character $model, $key, $index, $widget) {
            return $this->render(
                '_index_list_box',
                [
                    'model' => $model,
                    'key' => $key,
                    'index' => $index,
                    'widget' => $widget,
                ]
            );
        },
    ]) ?>
</div>
