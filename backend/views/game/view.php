<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Game */

$this->title = 'Session: ' . Html::encode($model->time);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_GAME_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'epic_id',
            'time',
            'status',
        ],
    ]) ?>

    <div class="text-center">
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

    <h2>Details</h2>
    <?= $model->details; ?>

    <h2>Notes</h2>
    <?= $model->note; ?>

</div>
