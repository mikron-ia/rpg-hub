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
        ]),
        'filterPosition' => null,
        'columns' => [
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, DescriptionHistory $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            '#',
                            [
                                'title' => Yii::$app->formatter->asDatetime($model->created_at),
                                'data-toggle' => 'popover',
                                'data-content' => $model->getPublicFormatted() . '<hr>' . $model->getPrivateFormatted(),
                                'data-html' => 'true',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php $this->registerJs("$(document).ready(function(){
                $('[data-toggle=\"popover\"]').popover();
            });"); ?>

</div>
