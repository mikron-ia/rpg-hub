<?php

use backend\assets\StoryAsset;
use yii\helpers\Html;

StoryAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'STORY_TITLE_INDEX'),
    'url' => ['story/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'STORY_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'STORY_DESCRIPTIONS_TAB'),
        'content' => $this->render('_view_texts', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'STORY_STATISTICS'),
        'content' => $this->render('_view_statistics', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= \yii\bootstrap\Tabs::widget([
    'items' => $items
]) ?>
