<?php

use common\models\AnnouncementQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/* @var $this View */
/* @var $searchModel AnnouncementQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'ANNOUNCEMENT_TITLE_INDEX');
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->params['activeEpic']->name,
    'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="announcement-index col-lg-10 col-lg-offset-1 col-md-12">

    <div class="buttoned-header"><h1><?= Html::encode($this->title) ?></h1></div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'ANNOUNCEMENTS_NOT_FOUND') . '</p>',
        'itemOptions' => ['class' => 'item'],
        'summary' => '',
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render(
                '_index_box',
                [
                    'model' => $model,
                    'key' => $key,
                    'index' => $index,
                    'widget' => $widget,
                ]
            );
        },
    ]) ?>
</div>
