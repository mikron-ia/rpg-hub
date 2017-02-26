<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Scenario */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SCENARIO_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = [
    [
        'label' => Yii::t('app', 'SCENARIO_DESCRIPTIONS_TAB'),
        'content' => $this->render('_view_descriptions', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'SCENARIO_TECHNICAL_DETAILS'),
        'content' => $this->render('_view_details', [
            'model' => $model,
        ]),
        'encode' => false,
        'active' => false,
    ],
];
?>
<div class="scenario-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a(
                Yii::t('app', 'BUTTON_DELETE'),
                ['delete', 'id' => $model->scenario_id],
                [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'id' => $model->scenario_id],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <p class="subtitle"><?= $model->tag_line; ?></p>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]) ?>

</div>
