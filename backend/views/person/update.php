<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
/* @var $epicListForSelector string[] */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Person',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_PEOPLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->person_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>
<div class="person-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'epicListForSelector' => $epicListForSelector,
    ]) ?>

</div>
