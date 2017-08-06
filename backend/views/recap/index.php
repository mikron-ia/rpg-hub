<?php

use common\models\Recap;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RecapQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'RECAP_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
$mostRecent = $searchModel->mostRecent();
?>
<div class="recap-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_RECAP_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterPosition' => null,
            'columns' => [
                [
                    'attribute' => 'position',
                    'contentOptions' => ['class' => 'text-center'],
                    'label' => Yii::t('app', 'RECAP_ORDER'),
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'time',
                    'contentOptions' => ['class' => 'text-center text-nowrap'],

                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'contentOptions' => ['class' => 'text-center'],
                    'buttons' => [
                        'view' => function ($url, Recap $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['recap/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
