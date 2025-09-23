<?php

use backend\assets\ScenarioAsset;
use common\models\Scenario;
use yii\helpers\Html;
use yii\web\View;

ScenarioAsset::register($this);

/* @var $this View */
/* @var $model Scenario */

$this->title = Yii::t('app', 'SCENARIO_TITLE_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'SCENARIO_INDEX_TITLE'),
    'url' => ['scenario/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>
<div class="scenario-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
