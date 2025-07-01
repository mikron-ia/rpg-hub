<?php

use common\models\Announcement;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\AnnouncementQuery $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'ANNOUNCEMENT_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="announcement-index">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'ANNOUNCEMENT_BUTTON_CREATE'),
            ['create'],
            ['class' => 'btn btn-success'],
        ); ?>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'columns' => [
                'title',
                'visible_from:datetime',
                'visible_to:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, Announcement $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['announcement/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Announcement $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['announcement/update', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>
