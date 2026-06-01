<?php

use backend\assets\ProjectAsset;
use common\models\Project;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

ProjectAsset::register($this);

/* @var $this View */
/* @var $model Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'PROJECT_TITLE_INDEX'),
    'url' => ['project/index', 'epic' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'PROJECT_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'PROJECT_DESCRIPTIONS_TAB'),
        'content' => $this->render('_view_texts', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'PROJECT_STATISTICS'),
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
