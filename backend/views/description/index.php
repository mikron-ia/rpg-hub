<?php

use common\models\Description;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DescriptionQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Descriptions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="description-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <div class="col-md-12" id="explanation">
        <p><?= Yii::t('app', 'DESCRIPTION_LIST_EXPLANATION'); ?> </p>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'columns' => [
                'descriptionPack.name',
                [
                    'attribute' => 'code',
                    'value' => function (Description $model) {
                        return $model->getTypeName();
                    }
                ],
                'lang',
                'visibility',
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
