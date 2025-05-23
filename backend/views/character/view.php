<?php

use backend\assets\CharacterAsset;
use yii\helpers\Html;

CharacterAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Character */
/* @var $externalDataDataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_CHARACTER_INDEX'),
    'url' => ['character/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'CHARACTER_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
            'externalDataDataProvider' => $externalDataDataProvider
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'CHARACTER_DESCRIPTIONS_TAB'),
        'content' => $this->render('../description/_view_descriptions_empty', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'CHARACTER_GROUPS_TAB'),
        'content' => $this->render('_view_groups', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'CHARACTER_STATISTICS'),
        'content' => $this->render('_view_statistics', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];
?>
<div class="character-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="subtitle"><?= $model->tagline; ?></p>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]) ?>

</div>

