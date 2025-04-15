<?php

use backend\assets\ScenarioAsset;
use yii\helpers\Html;

ScenarioAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Scenario */

$this->title = Yii::t('app', 'SCENARIO_TITLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'SCENARIO_INDEX_TITLE'),
    'url' => ['scenario/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
