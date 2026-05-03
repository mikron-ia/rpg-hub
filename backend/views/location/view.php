<?php

use backend\assets\LocationAsset;
use common\models\Location;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

LocationAsset::register($this);

/* @var $this View */
/* @var $model Location */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_LOCATIONS_INDEX'),
    'url' => ['location/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'LOCATION_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'LOCATION_STATISTICS'),
        'content' => $this->render('_view_statistics', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];
?>
<div class="location-view">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <?= Tabs::widget(['items' => $items]) ?>
</div>
