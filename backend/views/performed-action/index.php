<?php

use common\models\PerformedAction;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PerformedActionQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'PERFORMED_ACTIONS_TITLE_INDEX');
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
                'object_id',
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
