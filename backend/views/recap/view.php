<?php

use common\models\Seen;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Recap */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'RECAP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recap-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->recap_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->recap_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'key',
            ],
            [
                'attribute' => 'epic_id',
                'format' => 'raw',
                'value' => Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []),
            ],
            [
                'attribute' => 'time',
                'format' => 'raw',
                'value' => (new DateTime($model->time))->format("l, Y-m-d H:i:s")
            ],
        ],
    ]) ?>

    <h2><?= Yii::t('app', 'LABEL_CONTENT'); ?></h2>

    <div>
        <?= $model->getDataFormatted(); ?>
    </div>

    <div class="col-md-12">

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

        </div>

        <div class="col-md-6">

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

        </div>

        <div class="col-md-6">

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

    </div>

</div>
