<?php

use common\models\Character;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_CHARACTER_INDEX');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="person-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'CHARACTER_BUTTON_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'rowOptions' => function (Character $model, $key, $index, $grid) {
                return [
                    'data-toggle' => 'tooltip',
                    'title' => StringHelper::truncateWords($model->tagline, 5, ' (...)', false),
                ];
            },
            'columns' => [
                [
                    'attribute' => 'name',
                    'value' => function (Character $model) {
                        return StringHelper::truncateWords($model->name, 4, ' (...)', false);
                    },
                ],
                [
                    'attribute' => 'visibility',
                    'value' => function (Character $model) {
                        return $model->getVisibilityName();
                    }
                ],
                [
                    'attribute' => 'importance',
                    'value' => function (Character $model) {
                        return $model->getImportance();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                ],
            ],
        ]); ?>

        <?php $this->registerJs("$(document).ready(function(){
                $('[data-toggle=\"tooltip\"]').tooltip();
            });"); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>