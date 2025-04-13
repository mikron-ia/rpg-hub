<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheet */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX'),
    'url' => ['character-sheet/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;

$tabs = $model->presentExternal();

$active = true;

$items = [];

foreach ($tabs as $tabName => $tabData) {
    $item = [
        'label' => $tabData->title,
        'content' => '<div class="external-data-tab-container">' . \yii\helpers\HtmlPurifier::process($tabData->getContent()) . '</div>',
        'encode' => false,
        'active' => $active,
    ];

    if ($active) {
        $active = false;
    }

    $items[] = $item;
}

$items[] = [
    'label' => Yii::t('app', 'CHARACTER_SHEET_RAW_DATA'),
    'content' => $this->render('_view_data', ['model' => $model]),
    'encode' => true,
    'active' => false,
];

$items[] = [
    'label' => Yii::t('app', 'CHARACTER_SHEET_TECHNICAL'),
    'content' => $this->render('_view_gm', ['model' => $model]),
    'encode' => false,
    'active' => $active,
];

?>
<div class="character-sheet-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'key' => $model->key],
            ['class' => 'btn btn-primary']
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'key' => $model->key],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_CREATE_CHARACTER'),
            ['create-character', 'key' => $model->key],
            [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_CREATE_CHARACTER'),
                    'method' => 'post',
                ],
            ]
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_LOAD'),
            ['load-data', 'key' => $model->key],
            ['class' => 'btn btn-primary']
        ) ?>

        <?= \yii\helpers\Html::a(
            Yii::t('app', 'BUTTON_SEE_FRONTEND'),
            Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                'character-sheet/view',
                'key' => $model->key
            ]),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]) ?>

</div>
