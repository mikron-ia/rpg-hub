<?php

use backend\assets\GroupAsset;
use common\models\Epic;
use common\models\Group;
use yii\grid\GridView;
use yii\helpers\Html;

GroupAsset::register($this);

/* @var $epic Epic */
/* @var $this yii\web\View */
/* @var $searchModel common\models\GroupQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_GROUPS_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GROUP_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success']
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
            'columns' => [
                'name',
                [
                    'attribute' => 'master_group_id',
                    'format' => 'raw',
                    'value' => function (Group $model) {
                        return $model->masterGroup ?? '<em>&mdash;</em>';
                    }
                ],
                [
                    'attribute' => 'visibility',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function (Group $model) {
                        return $model->getVisibility();
                    }
                ],
                [
                    'attribute' => 'importance_category',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function (Group $model) {
                        return $model->getImportanceCategory();
                    }
                ],
                [
                    'label' => Yii::t('app', 'LABEL_COMPLETION'),
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'format' => 'raw',
                    'value' => function (Group $model) {
                        $count = $model->descriptionPack->getUniqueDescriptionTypesCount();

                        return '<span class="label ' . $model->getImportanceCategoryObject()->getClassForDescriptionCounter($count) . '">'
                            . $count
                            . ' / ' . $model->getImportanceCategoryObject()->minimum()
                            . '</span>';
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, Group $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['group/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Group $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['group/update', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
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
