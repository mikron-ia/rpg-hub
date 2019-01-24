<?php

/* @var $model \common\models\Character */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div id="people">
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'CHARACTERS_NOT_AVAILABLE') . '</p>',
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (\common\models\Character $model, $key, $index, $widget) {
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