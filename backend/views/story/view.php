<?php

use backend\assets\StoryAsset;
use common\models\Story;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

StoryAsset::register($this);

/* @var $this View */
/* @var $model Story */
/* @var $storyCharactersPublic array<string> */
/* @var $storyCharactersPrivate array<string> */
/* @var $storyGroupsPublic array<string> */
/* @var $storyGroupsPrivate array<string> */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'STORY_TITLE_INDEX'),
    'url' => ['story/index', 'epic' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'STORY_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
            'storyCharactersPublic' => $storyCharactersPublic,
            'storyCharactersPrivate' => $storyCharactersPrivate,
            'storyGroupsPublic' => $storyGroupsPublic,
            'storyGroupsPrivate' => $storyGroupsPrivate,
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

<?= Tabs::widget([
    'items' => $items
]) ?>
