<?php

/* @var $model \common\models\Person */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div id="people">
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (\common\models\Person $model, $key, $index, $widget) {
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