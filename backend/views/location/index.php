<?php

use backend\assets\LocationAsset;
use common\models\Epic;
use common\models\Location;
use common\models\LocationQuery;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

LocationAsset::register($this);

/* @var $epic Epic */
/* @var $this View */
/* @var $searchModel LocationQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_LOCATIONS_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'LOCATION_BUTTON_SEE_IMPORTANCES'),
            ['index-importance', 'epic' => $epic->key],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_LOCATION_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success']
        ) ?>
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
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'rowOptions' => function (Location $model, $key, $index, $grid) {
                return [
                    'data-copy-key' => sprintf('LOC:%s', $model->key),
                ];
            },
            'columns' => [
                'name',
                [
                    'attribute' => 'visibility',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function (Location $model) {
                        return $model->getVisibilityName();
                    }
                ],
                [
                    'attribute' => 'importance_category',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function (Location $model) {
                        return $model->getImportanceCategory();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['class' => 'action-cell'],
                    'template' => '{view} {update} {copy}',
                    'buttons' => [
                        'view' => function ($url, Location $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['location/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Location $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['location/update', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                        'copy' => function ($url, Location $model, $key) {
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
        <?php echo $this->render('_search', ['model' => $searchModel, 'epic' => $epic]); ?>
    </div>

    <div class="col-md-3" id="copy-key-disabled" style="display: none;">
        <p class="warning-box"><?= Yii::t('app', 'LABEL_COPY_KEY_DISABLED') ?></p>
    </div>

</div>
