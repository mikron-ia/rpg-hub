<?php

use common\models\Group;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/* @var $model Group */
/* @var $dataProvider ActiveDataProvider */
?>

<div id="groups">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'GROUPS_NOT_FOUND') . '</p>',
        'layout' => '<div class="object-list-box">{summary}  <ul class="object-list-wall">{items}</ul> {pager}</div>',
        'itemView' => function (Group $model, $key, $index, $widget) {
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
