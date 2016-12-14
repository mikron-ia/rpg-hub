<?php
use common\models\Seen;

/* @var $this yii\web\View */
/* @var $model common\models\Character */
?>

<div class="col-md-6">

    <h2 class="text-center"><?= Yii::t('app', 'SEEN_SIGHTINGS_ONLY') ?></h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->seenPack->getSightingsForSightings(),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            'seen_at:datetime',
            [
                'attribute' => 'status',
                'value' => function (Seen $model) {
                    return $model->getName();
                }
            ],
        ],
    ]) ?>

</div>

<div class="col-md-6">

    <h2 class="text-center"><?= Yii::t('app', 'SEEN_LISTINGS_ONLY') ?></h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->seenPack->getSightingsForNotices(),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            'noted_at:datetime',
            [
                'attribute' => 'status',
                'value' => function (Seen $model) {
                    return $model->getName();
                }
            ],
        ],
    ]) ?>

</div>
