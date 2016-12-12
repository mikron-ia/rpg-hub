<?php
use common\models\Seen;

/* @var $this yii\web\View */
/* @var $model common\models\Character */
?>

<div>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->seenPack->getSightings(),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            'noted_at:datetime',
            'seen_at:datetime',
            [
                'attribute' => 'status',
                'value' => function(Seen $model) {
                    return $model->getName();
                }
            ],
        ],
    ]) ?>
</div>
