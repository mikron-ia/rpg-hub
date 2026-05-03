<?php

use backend\assets\LocationAsset;
use common\models\Location;
use yii\helpers\Html;
use yii\web\View;

LocationAsset::register($this);

/* @var $this View */
/* @var $model Location */

$this->title = Yii::t('app', 'LABEL_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_LOCATIONS_INDEX'),
    'url' => ['location/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>
<div class="location-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
