<?php

/* @var $this yii\web\View */

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
        'summary' => '',
        'columns' => [
            'created_at:datetime',
            [
                'attribute' => 'visibility',
                'value' => function (GroupMembershipHistory $model) {
                    return $model->getVisibilityName();
                }
            ],
            [
                'attribute' => 'status',
                'value' => function (GroupMembershipHistory $model) {
                    return $model->getStatus();
                }
            ],
            [
                'value' => function (GroupMembershipHistory $model) {
                    $public = Html::tag('span', '', [
                        'title' => $model->getAttributeLabel('public_text'),
                        'data-toggle' => 'popover',
                        'data-content' => $model->getPublicFormatted(),
                        'data-html' => 'true',
                        'data-placement' => 'auto top',
                        'data-trigger' => 'click hover',
                        'class' => ['glyphicon', 'glyphicon-eye-open'],
                    ]);

                    $private = Html::tag('span', '', [
                        'title' => $model->getAttributeLabel('private_text'),
                        'data-toggle' => 'popover',
                        'data-content' => $model->getPrivateFormatted(),
                        'data-html' => 'true',
                        'data-placement' => 'auto top',
                        'data-trigger' => 'click hover',
                        'class' => ['glyphicon', 'glyphicon-eye-close'],
                    ]);

                    $short = Html::tag('span', '', [
                        'title' => $model->getAttributeLabel('short_text'),
                        'data-toggle' => 'popover',
                        'data-content' => $model->short_text,
                        'data-html' => 'true',
                        'data-placement' => 'auto top',
                        'data-trigger' => 'click hover',
                        'class' => ['glyphicon', 'glyphicon-flash'],
                    ]);

                    return $short . $public . $private;
                },
                'format' => 'raw',
            ]
        ],
    ]); ?>

    <?php $this->registerJs("$(document).ready(function(){
                $('[data-toggle=\"popover\"]').popover();
            });"); ?>

    <?php $this->registerJs("$('#membership-history-modal-title').html('" . $this->title . ": " . $model->character->name . "');"); ?>

</div>
