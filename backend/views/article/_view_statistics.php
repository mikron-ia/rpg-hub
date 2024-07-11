<?php

/* @var $this yii\web\View */

/* @var $model common\models\Article */

use common\models\core\SeenStatus;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

?>

<div class="col-md-6">
    <h2 class="text-center"><?= Yii::t('app', 'SEEN_READ') ?></h2>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_SEEN),
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
</div>

<div class="col-md-6">
    <h2 class="text-center"><?= Yii::t('app', 'SEEN_BEFORE_UPDATE') ?></h2>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_UPDATED),
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
</div>

<div class="col-md-6">
    <h2 class="text-center"><?= Yii::t('app', 'SEEN_NEW') ?></h2>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_NEW),
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
