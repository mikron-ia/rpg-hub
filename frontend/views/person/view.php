<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Person */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_PEOPLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $model->canUserControlYou();

$items = [
    [
        'label' => Yii::t('app', 'CHARACTER_DESCRIPTIONS_TAB'),
        'content' => $this->render('_view_descriptions', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('external', 'CHARACTER_REPUTATIONS_TAB'),
        'content' => '<div class="reputations"></div>',
        'headerOptions' => ['class' => 'tab-reputation hidden'],
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('external', 'CHARACTER_REPUTATION_EVENTS_TAB'),
        'content' => '<div class="reputation-events"></div>',
        'headerOptions' => ['class' => 'tab-reputation-events hidden'],
        'encode' => false,
        'active' => false,
    ],
];

if ($this->params['showPrivates']) {
    $items[] = [
        'label' => Yii::t('app', 'CHARACTER_GM_TAB'),
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

    <p class="subtitle"><?= $model->tagline; ?></p>

    <?= Tabs::widget([
        'items' => $items
    ]) ?>

    <?= $this->registerJs("$.get(
        '" . Yii::$app->urlManager->createUrl(['person/external-reputation']) . "',
        {id: " . $model->character_id . "},
        function (data) {
            $('.reputations').html(data);
        }
    ).success(function() {
        $('.tab-reputation').removeClass('hidden');
    });"); ?>

    <?= $this->registerJs("$.get(
        '" . Yii::$app->urlManager->createUrl(['person/external-reputation-event']) . "',
        {id: " . $model->character_id . "},
        function (data) {
            $('.reputation-events').html(data);
        }
    ).success(function() {
        $('.tab-reputation-events').removeClass('hidden');
    });"); ?>

</div>
