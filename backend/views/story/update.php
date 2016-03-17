<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = Yii::t('app', 'TITLE_UPDATE {modelClass}: ', [
    'modelClass' => 'Story',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->story_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BUTTON_UPDATE');
?>
<div class="story-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
