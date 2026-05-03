<?php

use common\models\Location;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/* @var $model Location */
/* @var $dataProvider ActiveDataProvider */

?>

<div id="locations">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'LOCATIONS_NOT_FOUND') . '</p>',
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (Location $model, $key, $index, $widget) {
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
