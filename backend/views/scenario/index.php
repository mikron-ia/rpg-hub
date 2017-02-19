<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ScenarioQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'SCENARIO_INDEX_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'SCENARIO_BUTTON_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'columns' => [
                'scenario_id',
                'epic_id',
                'name',
                'tag_line',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>

    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
