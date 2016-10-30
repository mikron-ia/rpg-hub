<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'STORY_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($model->canUserViewYou()) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'key',
                'name',
            ],
        ]);
    } ?>

    <h2><?= Yii::t('app', 'STORY_HEADER_SHORT'); ?></h2>
    <?= $model->short; ?>

    <h2><?= Yii::t('app', 'STORY_HEADER_LONG'); ?></h2>
    <?= $model->long; ?>

</div>
