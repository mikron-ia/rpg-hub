<?php

use backend\assets\ArticleAsset;
use common\models\Article;
use common\models\Epic;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $epic Epic */
/* @var $searchModel common\models\ArticleQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

ArticleAsset::register($this);

$this->title = Yii::t('app', 'ARTICLE_TITLE_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'ARTICLE_BUTTON_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success'],
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterPosition' => null,
        'rowOptions' => function (Article $model, $key, $index, $grid) {
            return [
                'data-copy-key' => sprintf('ART:%s', $model->key),
            ];
        },
        'columns' => [
            'title',
            [
                'attribute' => 'visibility',
                'value' => function (Article $model) {
                    return $model->getVisibilityName();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'action-cell'],
                'template' => '{view} {update} {copy} {up} {down}',
                'buttons' => [
                    'up' => function ($url, Article $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-up"></span>',
                            ['article/move-down', 'key' => $model->key],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_UP'),
                            ]
                        );
                    },
                    'down' => function ($url, Article $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-down"></span>',
                            ['article/move-up', 'key' => $model->key],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_DOWN'),
                            ]
                        );
                    },
                    'copy' => function ($url, Article $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-copy index-copy-key"></span>',
                            '#',
                            ['title' => Yii::t('app', 'BUTTON_COPY_KEY')]
                        );
                    },
                    'view' => function ($url, Article $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['article/view', 'key' => $model->key]),
                            ['title' => Yii::t('app', 'BUTTON_VIEW')]
                        );
                    },
                    'update' => function ($url, Article $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['article/update', 'key' => $model->key]),
                            ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                        );
                    },
                ]
            ],
        ],
    ]); ?>

    <div class="col-md-12" id="copy-key-disabled" style="display: none;">
        <p class="warning-box"><?= Yii::t('app', 'LABEL_COPY_KEY_DISABLED') ?></p>
    </div>
</div>
