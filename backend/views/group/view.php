<?php

use backend\assets\GroupAsset;
use common\models\Group;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

GroupAsset::register($this);

/* @var $this View */
/* @var $model Group */
/* @var $storyGroupPublic array<string> */
/* @var $storyGroupPrivate array<string> */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_GROUPS_INDEX'),
    'url' => ['group/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'GROUP_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
            'storyGroupPublic' => $storyGroupPublic,
            'storyGroupPrivate' => $storyGroupPrivate,
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
    <?= Tabs::widget(['items' => $items]) ?>
</div>
