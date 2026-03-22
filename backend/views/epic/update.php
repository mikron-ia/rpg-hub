<?php

use common\models\Epic;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Epic */

$this->title = Yii::t('app', 'LABEL_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>
<div class="epic-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
