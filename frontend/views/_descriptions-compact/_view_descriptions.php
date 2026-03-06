<?php

use common\models\core\HasDescriptions;
use common\models\Description;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\ListView;

/* @var $this View */
/* @var $model HasDescriptions */
/* @var $showPrivates bool */

?>

<div id="descriptions">
    <?= ListView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->getDescriptionsVisibleForCompact(),
            'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
        ]),
        'emptyText' => '<p class="info-box">' . Yii::t('app', 'DESCRIPTIONS_NOT_FOUND') . '</p>',
        'itemOptions' => ['class' => 'item'],
        'summary' => '',
        'itemView' => function (Description $model, $key, $index, $widget) use ($showPrivates) {
            return $this->render(
                '_view_description',
                [
                    'model' => $model,
                    'key' => $key,
                    'index' => $index,
                    'widget' => $widget,
                    'showPrivates' => $showPrivates,
                ]
            );
        },
    ]) ?>
</div>
