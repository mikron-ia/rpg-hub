<?php

/* @var $this yii\web\View */
use common\models\DescriptionHistory;
use common\models\GroupMembershipHistory;
use yii\helpers\Html;

/* @var $model common\models\GroupMembership */
/* @var $historyRecords \yii\db\ActiveQuery */

$this->title = Yii::t('app', 'GROUP_MEMBERSHIP_HISTORY_TITLE_INDEX');
?>
<div class="description-update">

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $historyRecords,
            'sort' => false,
            'pagination' => false,
        ]),
        'filterPosition' => null,
        'rowOptions' => function (GroupMembershipHistory $model, $key, $index, $grid) {
            return [
                'title' => Yii::$app->formatter->asDatetime($model->created_at),
                'data-toggle' => 'popover',
                'data-content' => $model->getPublicFormatted() . '<hr>' . $model->getPrivateFormatted(),
                'data-html' => 'true',
                'data-placement' => 'auto left',
                'data-trigger' => 'click hover',
            ];
        },
        'summary' => '',
        'columns' => [
            'created_at:datetime',
            [
                'attribute' => 'visibility',
                'value' => function (GroupMembershipHistory $model) {
                    return $model->getVisibility();
                }
            ],
        ],
    ]); ?>

    <?php $this->registerJs("$(document).ready(function(){
                $('[data-toggle=\"popover\"]').popover();
            });"); ?>

</div>
