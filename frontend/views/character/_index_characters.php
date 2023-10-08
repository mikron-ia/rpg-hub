<?php

/* @var $model Character */
/* @var $dataProvider yii\data\ActiveDataProvider */

use common\models\Character;
use yii\widgets\ListView;

?>

<div id="people">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'CHARACTERS_NOT_AVAILABLE') . '</p>',
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (Character $model, $key, $index, $widget) {
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
