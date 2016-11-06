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
        'label' => Yii::t('app', 'PERSON_DESCRIPTIONS_TAB'),
        'content' => $this->render('_view_descriptions', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('external', 'PERSON_REPUTATIONS_TAB'),
        'content' => '<div class="reputations"></div>',
        'headerOptions' => ['class' => 'tab-reputation'],
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('external', 'PERSON_REPUTATION_EVENTS_TAB'),
        'content' => '<div class="reputation-events"></div>',
        'headerOptions' => ['class' => 'tab-reputation-events'],
        'encode' => false,
        'active' => false,
    ],
];

if ($this->params['showPrivates']) {
    $items[] = [
        'label' => Yii::t('app', 'PERSON_GM_TAB'),
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
        {id: " . $model->person_id . "},
        function (data) {
            $('.reputations').html(data);
        }
    ).fail(function() {
        $('.tab-reputation').hide();
    });"); ?>

    <?= $this->registerJs("$.get(
        '" . Yii::$app->urlManager->createUrl(['person/external-reputation-event']) . "',
        {id: " . $model->person_id . "},
        function (data) {
            $('.reputation-events').html(data);
        }
    ).fail(function() {
        $('.tab-reputation-events').hide();
    });"); ?>

</div>
