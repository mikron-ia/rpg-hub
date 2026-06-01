<?php

use common\models\Character;
use common\models\core\SeenStatus;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Story */

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
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'times_seen',
                'format' => 'integer',
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'times_seen_since_update',
                'format' => 'integer',
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
            ],
        ],
    ]) ?>

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
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'times_seen',
                'format' => 'integer',
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'times_seen_since_update',
                'format' => 'integer',
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
            ],
        ],
    ]) ?>

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

<div class="col-md-6">
    <h2>JSON</h2>
    <pre class="wrapped-json"><?= Html::encode(json_encode(json_decode($model->data), JSON_PRETTY_PRINT)) ?></pre>
</div>
