<?php

use common\models\PerformedAction;
use common\models\PerformedActionQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $searchModel PerformedActionQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'PERFORMED_ACTIONS_TITLE_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/front', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="performed-action-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'columns' => [
                'performed_at:datetime',
                'user.username',
                [
                    'attribute' => 'operation',
                    'value' => function (PerformedAction $model) {
                        return $model->getName();
                    }
                ],
                'class',
                [
                    'attribute' => 'object_id',
                    'enableSorting' => false,
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
