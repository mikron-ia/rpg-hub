<?php

use backend\assets\StoryAsset;
use yii\helpers\Html;

StoryAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = Yii::t('app', 'STORY_TITLE_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'STORY_TITLE_INDEX'),
    'url' => ['story/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>
<div class="story-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
