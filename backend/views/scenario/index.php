<?php

use backend\assets\ScenarioAsset;
use common\models\Epic;
use common\models\Scenario;
use yii\grid\GridView;
use yii\helpers\Html;

ScenarioAsset::register($this);

/* @var $epic Epic */
/* @var $this yii\web\View */
/* @var $searchModel \common\models\ScenarioQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'SCENARIO_INDEX_TITLE');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'SCENARIO_BUTTON_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <div class="col-md-12">
        <p><?= Yii::t('app', 'SCENARIOS_EXPLANATION') ?></p>
    </div>

    <div class="col-md-9">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'rowOptions' => function (Scenario $model, $key, $index, $grid) {
                return [
                    'data-toggle' => 'tooltip',
                    'title' => $model->tag_line,
                ];
            },
            'columns' => [
                'name',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function (Scenario $model) {
                        return $model->getStatus();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, Scenario $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['scenario/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Scenario $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['scenario/update', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
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

</div>
