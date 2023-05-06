<?php

use backend\assets\CharacterAsset;
use common\models\Character;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;

CharacterAsset::register($this);


/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_CHARACTER_INDEX');
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->params['activeEpic']->name,
    'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]
];
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
                    'title' => $model->tagline,
                ];
            },
            'columns' => [
                [
                    'attribute' => 'name',
                    'value' => function (Character $model) {
                        return StringHelper::truncateWords($model->name, 7, ' (...)', false);
                    },
                ],
                [
                    'attribute' => 'visibility',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function (Character $model) {
                        return $model->getVisibilityName();
                    }
                ],
                [
                    'attribute' => 'importance_category',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function (Character $model) {
                        return $model->getImportanceCategory();
                    }
                ],
                [
                    'label' => Yii::t('app', 'LABEL_COMPLETION'),
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'format' => 'raw',
                    'value' => function (Character $model) {
                        $count = $model->descriptionPack->getUniqueDescriptionTypesCount();

                        return '<span class="label ' . $model->getImportanceCategoryObject()->getClassForDescriptionCounter($count) . '">'
                            . $count
                            . ' / ' . $model->getImportanceCategoryObject()->minimum()
                            . '</span>';
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['class' => 'text-center'],
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, Character $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['character/view', 'key' => $model->key]),
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