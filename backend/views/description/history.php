<?php

/* @var $this yii\web\View */
use common\models\DescriptionHistory;
use yii\helpers\Html;

/* @var $model common\models\Description */
/* @var $historyRecords \yii\db\ActiveQuery */

$this->title = Yii::t('app', 'DESCRIPTION_TITLE_UPDATE');
$this->params['breadcrumbs'][] = $model->getTypeName();
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="description-update">

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $historyRecords,
            'sort' => false,
            'pagination' => false,
        ]),
        'filterPosition' => null,
        'rowOptions' => function (DescriptionHistory $model, $key, $index, $grid) {
            return [
                'title' => Yii::$app->formatter->asDatetime($model->created_at),
                'data-toggle' => 'popover',
                'data-content' => $model->getPublicFormatted() . '<hr>' . $model->getPrivateFormatted(),
                'data-html' => 'true',
                'data-placement' => 'auto bottom',
                'data-trigger' => 'click hover',
            ];
        },
        'summary' => '',
        'columns' => [
            'created_at:datetime',
            [
                'attribute' => 'visibility',
                'value' => function (DescriptionHistory $model) {
                    return $model->getVisibility();
                }
            ],
        ],
    ]); ?>

    <?php $this->registerJs("$(document).ready(function(){
                $('[data-toggle=\"popover\"]').popover();
            });"); ?>

</div>
