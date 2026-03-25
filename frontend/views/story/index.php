<?php

use frontend\assets\StoryAsset;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

StoryAsset::register($this);

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'STORY_TITLE_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="story-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'STORIES_NOT_FOUND') . '</p>',
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render(
                '_epic_box',
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
