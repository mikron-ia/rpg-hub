<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $epicListForSelector string[] */

$this->title = Yii::t('app', 'STORY_TITLE_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'STORY_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->story_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="story-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'epicListForSelector' => $epicListForSelector,
    ]) ?>

</div>
