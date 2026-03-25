<?php

use backend\assets\StoryAsset;
use common\models\Story;
use yii\helpers\Html;
use yii\web\View;

StoryAsset::register($this);

/* @var $this View */
/* @var $model Story */

$this->title = Yii::t('app', 'STORY_TITLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'STORY_TITLE_INDEX'),
    'url' => ['story/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
