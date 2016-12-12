<?php

use common\models\core\Language;
use common\models\ExternalData;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Character */
/* @var $externalDataDataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_CHARACTER_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'CHARACTER_BASIC'),
        'content' => $this->render('_view_basic', [
            'model' => $model,
            'externalDataDataProvider' => $externalDataDataProvider
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'CHARACTER_DESCRIPTIONS_TAB'),
        'content' => $this->render('_view_descriptions', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'CHARACTER_SIGHTINGS'),
        'content' => $this->render('_view_views', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
];
?>
<div class="person-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="subtitle"><?= $model->tagline; ?></p>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]) ?>

</div>
