<?php

use common\models\Story;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Epic */

$this->title = Yii::t('app', 'LABEL_EPIC') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-view">

    <h1>
        <?= Html::encode($this->title) ?>
        <span class="pull-right">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_UPDATE'),
                    ['update', 'id' => $model->epic_id],
                    ['class' => 'btn btn-primary']);
                ?>
            </span>
    </h1>

    <p><b><?= $model->getAttributeLabel('key'); ?>:</b> <?= $model->key; ?></p>

    <p><b><?= $model->getAttributeLabel('system'); ?>:</b> <?= $model->system; ?></p>

    <div class="col-lg-6">
        <h2><?= Yii::t('app', 'EPIC_HEADER_STORIES'); ?></h2>
        <?= GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getStories()->orderBy('story_id DESC'),
                'sort' => false,
            ]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, Story $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['story/view', 'id' => $model->story_id]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Story $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['story/update', 'id' => $model->story_id]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-lg-6">
        <h2><?= Yii::t('app', 'EPIC_HEADER_RECAPS'); ?></h2>
        <?= GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getRecaps()->orderBy('time DESC'),
                'sort' => false,
            ]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'time',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['recap/view', 'id' => $model->recap_id]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['recap/update', 'id' => $model->recap_id]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

</div>
