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
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (Group $model, $key, $index, $widget) {
            return $this->render(
                '_index_box',
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
