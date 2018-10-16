<?php

use frontend\assets\CharacterAsset;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

CharacterAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Character */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_CHARACTER_INDEX'), 'url' => ['index', 'key' => $model->epic->key]];
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
        'label' => Yii::t('app', 'CHARACTER_GROUPS_TAB'),
        'content' => $this->render('_view_groups', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('external', 'CHARACTER_REPUTATIONS_TAB'),
        'content' => '<div class="reputations"></div>',
        'headerOptions' => ['class' => 'tab-reputation hidden', 'data-key' => $model->key],
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('external', 'CHARACTER_REPUTATION_EVENTS_TAB'),
        'content' => '<div class="reputation-events"></div>',
        'headerOptions' => ['class' => 'tab-reputation-events hidden', 'data-key' => $model->key],
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
        <?php if ($this->params['showPrivates']): ?>
            <?= Html::a(Yii::t('app', 'BUTTON_SECRETS_SHOW'), '#', [
                'class' => 'btn btn-default',
                'onclick' => 'showSecrets()',
                'id' => 'secrets-show',
            ]) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_SECRETS_HIDE'), '#', [
                'class' => 'btn btn-default',
                'onclick' => 'hideSecrets()',
                'id' => 'secrets-hide',
                'style' => 'display: none;'
            ]) ?>
        <?php endif; ?>
    </div>

    <p class="subtitle"><?= $model->tagline; ?></p>

    <?= Tabs::widget([
        'items' => $items
    ]) ?>

    <?php if ($this->params['showPrivates']): ?>
        <?= $this->registerJs('$(".secret").hide();'); ?>
    <?php endif; ?>

</div>
