<?php

use common\models\Epic;
use common\models\Game;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $epic Epic */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_GAME_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GAME_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'position',
                'contentOptions' => ['class' => 'text-center'],
                'label' => Yii::t('app', 'GAME_POSITION'),
            ],
            'basics',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (Game $model) {
                    return $model->getStatus();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {up} {down}',
                'buttons' => [
                    'view' => function ($url, Game $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['game/view', 'id' => $model->game_id]),
                            ['title' => Yii::t('app', 'BUTTON_VIEW')]
                        );
                    },
                    'update' => function ($url, Game $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['game/update', 'id' => $model->game_id]),
                            ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                        );
                    },
                    'up' => function ($url, Game $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-up"></span>',
                            ['game/move-down', 'id' => $model->game_id],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_UP'),
                            ]
                        );
                    },
                    'down' => function ($url, Game $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-down"></span>',
                            ['game/move-up', 'id' => $model->game_id],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_DOWN'),
                            ]
                        );
                    },
                ]
            ],
        ],
    ]); ?>
</div>
