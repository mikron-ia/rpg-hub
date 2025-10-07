<?php

use backend\assets\ScenarioAsset;
use common\models\Epic;
use common\models\Scenario;
use common\models\ScenarioQuery;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

ScenarioAsset::register($this);

/* @var $epic Epic */
/* @var $this View */
/* @var $searchModel ScenarioQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'SCENARIO_INDEX_TITLE');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/view', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'SCENARIOS_NOT_FOUND') . '</p>',
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render(
                '_index_box',
                [
                    'model' => $model,
                    'key' => $key,
                    'index' => $index,
                    'widget' => $widget,
                ]
            );
        },
    ]) ?>
</div>
