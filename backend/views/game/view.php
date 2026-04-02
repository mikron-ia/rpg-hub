<?php

use common\models\Game;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Game */

$this->title = Html::encode($model->basics);
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_GAME_INDEX'),
    'url' => ['game/index', 'epic' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-view">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'key' => $model->key], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'delete',
                ],
            ]) ?>
        </div>
    </div>

    <div class="col-md-12">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'epic_id',
                        'format' => 'raw',
                        'value' => Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key], []),
                    ],
                    'planned_date',
                    'planned_location',
                    [
                        'attribute' => 'recap_id',
                        'format' => 'raw',
                        'value' => isset($model->recap)
                            ? Html::a(
                                $model->recap->name,
                                ['recap/view', 'key' => $model->recap->key], []
                            )
                            : Yii::t('app', 'RECAP_NOT_AVAILABLE'),
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => '<span class="table-tag game-status ' . $model->getStatusClass() . '">' . $model->getStatus() . '</span>',
                    ]
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $model->notesFormatted; ?>
        </div>
    </div>
</div>
