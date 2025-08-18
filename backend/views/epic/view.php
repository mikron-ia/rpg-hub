<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Epic */

$this->title = Yii::t('app', 'LABEL_EPIC') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['epic/front', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'EPIC_DETAILS');

$items = [
    [
        'label' => Yii::t('app', 'EPIC_BASIC'),
        'content' => $this->render('_view_basic', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'EPIC_STATISTICS'),
        'content' => $this->render('_view_statistics', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];
?>
<div class="epic-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary'],
            ) ?>
        </div>
    </div>

    <?= Tabs::widget([
        'items' => $items
    ]) ?>

</div>
