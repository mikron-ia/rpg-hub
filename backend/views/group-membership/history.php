<?php

use common\models\GroupMembership;
use common\models\GroupMembershipHistory;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model GroupMembership */
/* @var $historyRecords ActiveQuery */

$this->title = Yii::t('app', 'GROUP_MEMBERSHIP_HISTORY_TITLE_INDEX');
?>
<div class="description-update">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
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
