<?php

use frontend\assets\StoryAsset;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

StoryAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'STORY_TITLE_INDEX'), 'url' => ['index', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $model->canUserControlYou();

$items = [
    [
        'label' => Yii::t('app', 'STORY_SHORT_TAB'),
        'content' => $this->render('_view_short', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'STORY_LONG_TAB'),
        'content' => $this->render('_view_long', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];

if ($this->params['showPrivates']) {
    $items[] = [
        'label' => Yii::t('app', 'CHARACTER_GM_TAB'),
        'content' => $this->render('_view_gm', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ];
}

?>
<div class="story-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Tabs::widget([
        'items' => $items
    ]) ?>
</div>
