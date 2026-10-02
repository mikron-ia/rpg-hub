<?php

use common\models\core\SeenStatus;
use common\models\Recap;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Recap */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'RECAP_TITLE_INDEX'),
    'url' => ['recap/index', 'epic' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recap-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'key' => $model->key],
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_DOWN'),
            ['recap/move-up', 'key' => $model->key],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>

        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_UP'),
            ['recap/move-down', 'key' => $model->key],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>

        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'key' => $model->key],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'key',
                ],
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key], []),
                ],
                [
                    'attribute' => 'pointInTime',
                    'format' => 'raw',
                    'value' => $model->pointInTime?->getLink(),
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
                [
                    'attribute' => 'position',
                ],
            ],
        ]) ?>
    </div>

    <?php if (!empty($model->games)) : ?>
        <div class="col-md-6">
            <h2 class="text-center"><?= Yii::t('app', 'LABEL_GAMES'); ?></h2>
            <ul>
                <?php foreach ($model->games as $game): ?>
                    <li><?= Html::a($game->basics, ['game/view', 'key' => $game->key], []) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="col-md-12">
        <h2><?= Yii::t('app', 'LABEL_CONTENT'); ?></h2>
        <?= $model->getContentFormattedForOperator(); ?>
    </div>

    <?php if (!empty($model->notes)) : ?>
        <div class="col-md-12">
            <h2><?= Yii::t('app', 'RECAP_NOTES'); ?></h2>

            <div>
                <?= $model->getNotesFormatted(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-12">
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
    </div>
</div>
