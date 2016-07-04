<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Parameter;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ParameterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'PARAMETER_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parameter-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'columns' => [
                'parameterPack.class',
                [
                    'attribute' => 'code',
                    'value' => function (Parameter $model) {
                        return $model->getTypeName();
                    }
                ],
                [
                    'attribute' => 'visibility',
                    'value' => function (Parameter $model) {
                        return $model->getVisibility();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}'
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>
