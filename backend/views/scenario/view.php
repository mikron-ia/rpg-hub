<?php

use backend\assets\ScenarioAsset;
use yii\helpers\Html;

ScenarioAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Scenario */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'SCENARIO_INDEX_TITLE'),
    'url' => ['scenario/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'SCENARIO_DESCRIPTIONS_TAB'),
        'content' => $this->render('../description/_view_descriptions_empty', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'SCENARIO_CONTENT_TAB'),
        'content' => $this->render('_view_text', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'SCENARIO_TECHNICAL_DETAILS'),
        'content' => $this->render('_view_details', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => false,
    ],
];
?>
<div class="scenario-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="subtitle"><?= $model->tag_line; ?></p>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]) ?>

</div>
