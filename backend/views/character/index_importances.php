<?php

use backend\assets\CharacterAsset;
use common\models\entities\CharacterWithImportance;
use common\models\Epic;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;

CharacterAsset::register($this);

/* @var $epic Epic */
/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_CHARACTER_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;

$columnsFixed = [
        [
                'attribute' => 'name',
                'value' => function (CharacterWithImportance $model) {
                    return StringHelper::truncateWords($model->name, 7, ' (...)', false);
                },
        ],
        [
                'attribute' => 'visibility',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
        ],
        [
                'attribute' => 'importance_category',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
        ],
];

$columnsVariable = [];
foreach ($epic->participants as $participant) {
    $columnsVariable[] = [
            'attribute' => sprintf(CharacterWithImportance::USER_NAME_PATTERN, $participant->user_id),
            'label' => $participant->user->username,
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
    ];
}

$columnsAction = [
        [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                        'view' => function ($url, CharacterWithImportance $model, $key) {
                            return Html::a(
                                    '<span class="glyphicon glyphicon-eye-open"></span>',
                                    Yii::$app->urlManager->createUrl(['character/view', 'key' => $model->key]),
                                    ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, CharacterWithImportance $model, $key) {
                            return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    Yii::$app->urlManager->createUrl(['character/update', 'key' => $model->key]),
                                    ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                ],
        ]
];

?>

<div class="person-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
                Yii::t('app', 'CHARACTER_BUTTON_SEE_MAIN_INDEX'),
                ['index', 'epic' => $epic->key],
                ['class' => 'btn btn-primary']
        ) ?>
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
                'rowOptions' => function (CharacterWithImportance $model, $key, $index, $grid) {
                    return ['data-toggle' => 'tooltip', 'title' => $model->tagline];
                },
                'columns' => array_merge($columnsFixed, $columnsVariable, $columnsAction),
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel, 'epic' => $epic, 'actionUrl' => 'index-importance']); ?>
    </div>
</div>