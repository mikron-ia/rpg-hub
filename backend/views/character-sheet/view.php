<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheet */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tabs = $model->presentExternal();

var_dump($tabs); die;

$active = true;

$items = [];

foreach ($tabs as $tabName => $tabData) {
    if (is_array($tabData)) {

        $item = [
            'label' => $tabName,
            'content' => $tabData, // @todo Next step - proper render with an object
            'encode' => false,
            'active' => $active,
        ];

        if ($active) {
            $active = false;
        }

        $items[] = $item;

    }
}

$items[] = [
    'label' => Yii::t('app', 'CHARACTER_SHEET_TECHNICAL'),
    'content' => $this->render('_view_gm', ['model' => $model]),
    'encode' => false,
    'active' => $active,
];

?>
<div class="character-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'key' => $model->key],
            ['class' => 'btn btn-primary']
        ); ?>
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
    </div>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]) ?>

</div>
