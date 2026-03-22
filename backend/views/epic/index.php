<?php

use common\models\Epic;
use common\models\EpicQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $searchModel EpicQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_EPICS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_EPIC_ADD'), ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterPosition' => null,
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'name',
            ],
            [
                'attribute' => 'system',
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (Epic $model) {
                    return $model->getStatus();
                }
            ],
            [
                'label' => Yii::t('app', 'EPIC_COUNT_GROUPS'),
                'value' => function (Epic $model) {
                    return $model->getGroups()->count();
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, Epic $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['epic/view', 'key' => $model->key]),
                            ['title' => Yii::t('app', 'BUTTON_VIEW')]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
