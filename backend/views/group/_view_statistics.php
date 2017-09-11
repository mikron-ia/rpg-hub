<?php

use common\models\Seen;

/* @var $this yii\web\View */
/* @var $model common\models\Group */

?>

<div class="col-md-6">

    <h2 class="text-center"><?= Yii::t('app', 'SEEN_READ') ?></h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->seenPack->getSightingsWithStatus(Seen::STATUS_SEEN),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            [
                'attribute' => 'seen_at',
                'format' => 'datetime',
                'enableSorting' => false,
            ],
        ],
    ]) ?>

    <h2 class="text-center"><?= Yii::t('app', 'SEEN_BEFORE_UPDATE') ?></h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->seenPack->getSightingsWithStatus(Seen::STATUS_UPDATED),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            [
                'attribute' => 'seen_at',
                'format' => 'datetime',
                'enableSorting' => false,
            ],
        ],
    ]) ?>

    <h2 class="text-center"><?= Yii::t('app', 'SEEN_NEW') ?></h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->seenPack->getSightingsWithStatus(Seen::STATUS_NEW),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            [
                'attribute' => 'noted_at',
                'format' => 'datetime',
                'enableSorting' => false,
            ],
        ],
    ]) ?>

</div>

<div class="col-md-6">

    <h2 class="text-center"><?= Yii::t('app', 'IMPORTANCE_VALUES_LIST') ?></h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->importancePack->getImportances(),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            [
                'attribute' => 'importance',
                'enableSorting' => false,
            ],
        ],
    ]) ?>

</div>
