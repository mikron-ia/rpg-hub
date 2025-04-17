<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'),
    'url' => ['article/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'ARTICLE_BASIC_TAB'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'ARTICLE_TEXT_TAB'),
        'content' => $this->render('_view_text', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'ARTICLE_STATISTICS_TAB'),
        'content' => $this->render('_view_statistics', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];

?>
<div class="article-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="subtitle"><?= $model->subtitle ?></p>

    <?= Tabs::widget([
        'items' => $items
    ]) ?>

</div>
