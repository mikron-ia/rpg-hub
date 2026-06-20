<?php

use common\models\CharacterSheet;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\View;

/* @var $this View */
/* @var $model CharacterSheet */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->params['activeEpic']->name,
    'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX'),
    'url' => ['index', 'key' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $model->canUserControlYou();

$tabs = $model->presentExternal();

$active = true;

$items = [];

foreach ($tabs as $tabName => $tabData) {
    $item = [
        'label' => $tabData->title,
        'content' => '<div class="external-data-tab-container">' . HtmlPurifier::process($tabData->getContent()) . '</div>',
        'encode' => false,
        'active' => $active,
    ];

    if ($active) {
        $active = false;
    }

    $items[] = $item;
}

?>
<div class="character-sheet-view">

    <div class="buttoned-header">
        <h1>
            <?= Html::encode($this->title) ?>
            <span class="view-state-tag <?= $model->getDataState()->getClass() ?>">
                <?= $model->getDataState()->getName() ?>
            </span>
        </h1>
    </div>

    <?php if ($items): ?>
        <?= Tabs::widget([
            'items' => $items,
        ]) ?>
    <?php else: ?>
        <p class="error-box"><?= Yii::t('app', 'CHARACTER_SHEET_NO_DATA') ?></p>
    <?php endif; ?>

</div>
