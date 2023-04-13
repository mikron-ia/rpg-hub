<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Game */

$this->title = Html::encode($model->basics);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_GAME_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'id' => $model->game_id],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->game_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
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
                        'value' => Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key], []),
                    ],
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
