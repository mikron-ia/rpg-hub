<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PointInTime */

$this->title = Yii::t('app', 'TITLE_POINT_IN_TIME_CREATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_POINT_IN_TIME_INDEX'),
    'url' => ['point-in-time/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-in-time-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
