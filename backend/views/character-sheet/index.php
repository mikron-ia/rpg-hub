<?php

use common\models\CharacterSheet;
use common\models\Epic;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $epic Epic */
/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterSheetQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-sheet-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_CHARACTER_SHEET_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success']
        ); ?>
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
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'label' => Yii::t('app', 'CHARACTER_SHEET_DATA_STATE'),
                    'format' => 'raw',
                    'value' => function (CharacterSheet $model) {
                        return $model->getDataState()->getName();
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, CharacterSheet $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['character-sheet/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, CharacterSheet $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['character-sheet/update', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel, 'epic' => $epic]); ?>
    </div>

</div>
