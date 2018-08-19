<?php

use backend\assets\GroupAsset;
use yii\helpers\Html;

GroupAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Group */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_GROUPS_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'GROUP_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'GROUP_DESCRIPTIONS_TAB'),
        'content' => $this->render('../description/_view_descriptions_empty', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'GROUP_MEMBERSHIPS'),
        'content' => $this->render('_view_members', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'GROUP_STATISTICS'),
        'content' => $this->render('_view_statistics', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];
?>
<div class="group-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]) ?>

    <?php $this->registerJs("$(document).ready(function(){
                $('[data-toggle=\"tooltip\"]').tooltip();
            });"); ?>

</div>
