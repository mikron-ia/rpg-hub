<?php

use common\models\Recap;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RecapQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'RECAP_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recap-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_RECAP_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterPosition' => null,
            'columns' => [
                'epic.name',
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'time',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
