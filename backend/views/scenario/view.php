<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Scenario */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SCENARIO_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'SCENARIO_DESCRIPTIONS_TAB'),
        'content' => $this->render('../description/_view_descriptions_empty', ['model' => $model]),
        'encode' => false,
        'active' => true,
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

<?php $this->registerJsFile('@web/js/description-user.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>