<?php

use common\models\Description;
use common\models\DescriptionHistory;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Description */
/* @var $historyRecords ActiveQuery */

$this->title = Yii::t('app', 'DESCRIPTION_TITLE_UPDATE');
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
                'value' => function (DescriptionHistory $model) {
                    return $model->getVisibilityName();
                }
            ],
            [
                'value' => function (DescriptionHistory $model) {
                    $public = Html::tag('span', '', [
                        'title' => $model->getAttributeLabel('public_text'),
                        'data-toggle' => 'popover',
                        'data-content' => $model->getPublicFormatted(),
                        'data-html' => 'true',
                        'data-placement' => 'auto top',
                        'data-trigger' => 'click hover',
                        'class' => ['glyphicon', 'glyphicon-folder-open'],
                    ]);

                    $protected = Html::tag('span', '', [
                        'title' => $model->getAttributeLabel('protected_text'),
                        'data-toggle' => 'popover',
                        'data-content' => $model->getProtectedFormatted(),
                        'data-html' => 'true',
                        'data-placement' => 'auto top',
                        'data-trigger' => 'click hover',
                        'class' => ['glyphicon', 'glyphicon-folder-close'],
                    ]);

                    $private = Html::tag('span', '', [
                        'title' => $model->getAttributeLabel('private_text'),
                        'data-toggle' => 'popover',
                        'data-content' => $model->getPrivateFormatted(),
                        'data-html' => 'true',
                        'data-placement' => 'auto top',
                        'data-trigger' => 'click hover',
                        'class' => ['glyphicon', 'glyphicon-lock'],
                    ]);

                    return $public . $protected . $private;
                },
                'format' => 'raw',
            ],
        ],
    ]); ?>

    <?php $this->registerJs("$(document).ready(function(){
                $('[data-toggle=\"popover\"]').popover();
            });"); ?>

</div>
