<?php

use backend\assets\ImageAsset;
use common\models\Epic;
use common\models\Image;
use common\models\ImageQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/* @var $epic Epic */
/* @var $this View */
/* @var $searchModel ImageQuery */
/* @var $dataProvider ActiveDataProvider */

ImageAsset::register($this);

$this->title = Yii::t('app', 'IMAGE_TITLE_INDEX');

$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-index">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_IMAGE_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success'],
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>
    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterPosition' => null,
            'rowOptions' => function (Image $model, $key, $index, $grid) {
                return [
                    'data-copy-key' => sprintf('IMG:%s', $model->key),
                ];
            },
            'columns' => [
                'name',
                [
                    'label' => Yii::t('app', 'IMAGE_LINK_COUNT'),
                    'value' => function (Image $model) {
                        return count($model->imageLinks);
                    },
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'datetime',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'class' => ActionColumn::class,
                    'contentOptions' => ['class' => 'action-cell'],
                    'template' => '{view} {update} {copy}',
                    'buttons' => [
                        'view' => function ($url, Image $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['image/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Image $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['image/update', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                        'copy' => function ($url, Image $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-copy index-copy-key"></span>',
                                '#',
                                ['title' => Yii::t('app', 'BUTTON_COPY_KEY')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <div class="col-md-3" id="copy-key-disabled" style="display: none;">
        <p class="warning-box"><?= Yii::t('app', 'LABEL_COPY_KEY_DISABLED') ?></p>
    </div>
</div>
