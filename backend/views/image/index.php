<?php

use common\models\Epic;
use common\models\Image;
use common\models\ImageQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/* @var $epic Epic */
/* @var $this View */
/* @var $searchModel ImageQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'IMAGE_TITLE_INDEX');
$this->params['breadcrumbs'][] = ['label' => $epic->name, 'url' => ['epic/front', 'key' => $epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-index">

    <div class="buttoned-header">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            Yii::t('app', 'BUTTON_IMAGE_CREATE'),
            ['create', 'epic' => $epic->key],
            ['class' => 'btn btn-success'],
        ); ?>
    </p>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterPosition' => null,
            'columns' => [
                'name',
                'display_height',
                'display_width',
                'created_at:datetime',
                'updated_at:datetime',
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => function ($action, Image $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'key' => $model->key]);
                    }
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
