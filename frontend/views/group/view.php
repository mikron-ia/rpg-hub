<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Group */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_GROUPS_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $model->canUserControlYou();

$items = [
    [
        'label' => Yii::t('app', 'GROUP_DESCRIPTIONS_TAB'),
        'content' => $this->render('_view_descriptions', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'GROUP_MEMBERSHIPS_TAB'),
        'content' => $this->render('_view_compositions', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('external', 'GROUP_REPUTATIONS_TAB'),
        'content' => '<div class="reputations"></div>',
        'headerOptions' => ['class' => 'tab-reputation hidden'],
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('external', 'GROUP_REPUTATION_EVENTS_TAB'),
        'content' => '<div class="reputation-events"></div>',
        'headerOptions' => ['class' => 'tab-reputation-events hidden'],
        'encode' => false,
        'active' => false,
    ],
];

if ($this->params['showPrivates']) {
    $items[] = [
        'label' => Yii::t('app', 'GROUP_GM_TAB'),
        'content' => $this->render('_view_gm', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ];
}

?>
<div class="person-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= Tabs::widget([
        'items' => $items
    ]) ?>

    <?= $this->registerJs("$.get(
        '" . Yii::$app->urlManager->createUrl(['group/external-reputation']) . "',
        {key: '" . $model->key . "'},
        function (data) {
            $('.reputations').html(data);
        }
    ).success(function() {
        $('.tab-reputation').removeClass('hidden');
    });"); ?>

    <?= $this->registerJs("$.get(
        '" . Yii::$app->urlManager->createUrl(['group/external-reputation-event']) . "',
        {key: '" . $model->key . "'},
        function (data) {
            $('.reputation-events').html(data);
        }
    ).success(function() {
        $('.tab-reputation-events').removeClass('hidden');
    });"); ?>

</div>
