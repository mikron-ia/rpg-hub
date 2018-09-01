<?php

use backend\assets\StoryAsset;
use common\models\Story;
use yii\helpers\Html;
use yii\grid\GridView;

StoryAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\RecapQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'STORY_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_STORY_CREATE'), ['create'], ['class' => 'btn btn-success']); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <div class="col-md-9" id="filter">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, Story $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['story/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Story $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['story/update', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
