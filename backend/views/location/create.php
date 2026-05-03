<?php

use backend\assets\LocationAsset;
use yii\helpers\Html;

LocationAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = Yii::t('app', 'TITLE_LOCATION_CREATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_LOCATIONS_INDEX'),
    'url' => ['location/index', 'epic' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
