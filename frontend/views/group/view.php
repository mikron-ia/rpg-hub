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
        <?php if ($this->params['showPrivates']): ?>
            <?= Html::a(Yii::t('app', 'BUTTON_SECRETS_SHOW'), '#', [
                'class' => 'btn btn-default',
                'onclick' => '$(".secret").show(); $("#secrets-show").hide(); $("#secrets-hide").show();',
                'id' => 'secrets-show',
            ]) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_SECRETS_HIDE'), '#', [
                'class' => 'btn btn-default',
                'onclick' => '$(".secret").hide(); $("#secrets-show").show(); $("#secrets-hide").hide();',
                'id' => 'secrets-hide',
                'style' => 'display: none;'
            ]) ?>
        <?php endif; ?>
    </div>

    <?= Tabs::widget([
        'items' => $items
    ]) ?>

    <?php $this->registerJs("$.get(
        '" . Yii::$app->urlManager->createUrl(['group/external-reputation']) . "',
        {key: '" . $model->key . "'},
        function (data) {
            $('.reputations').html(data);
        }
    ).done(function() {
        $('.tab-reputation').removeClass('hidden');
    });"); ?>

    <?php $this->registerJs("$.get(
        '" . Yii::$app->urlManager->createUrl(['group/external-reputation-event']) . "',
        {key: '" . $model->key . "'},
        function (data) {
            $('.reputation-events').html(data);
        }
    ).done(function() {
        $('.tab-reputation-events').removeClass('hidden');
    });"); ?>

    <?php if ($this->params['showPrivates']): ?>
        <?= $this->registerJs('$(".secret").hide();'); ?>
    <?php endif; ?>

</div>
