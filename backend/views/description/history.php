<?php

/* @var $this yii\web\View */
use common\models\DescriptionHistory;
use yii\helpers\Html;

/* @var $model common\models\Description */
/* @var $historyRecords \yii\db\ActiveQuery */

$this->title = Yii::t('app', 'DESCRIPTION_TITLE_UPDATE');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
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
        'summary' => '',
        'columns' => [
            'created_at:datetime',
            [
                'attribute' => 'visibility',
                'value' => function (DescriptionHistory $model) {
                    return $model->getVisibility();
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
