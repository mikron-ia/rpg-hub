<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'), 'url' => ['index', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="subtitle"><?= $model->subtitle ?></p>

    <div class="col-md-12">
        <?= $model->text_ready ?>
    </div>

</div>
