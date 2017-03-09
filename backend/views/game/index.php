<?php

use common\models\Game;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_GAME_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_GAME_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'time',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (Game $model) {
                    return Html::tag('span', $model->getStatus(), ['class' => ['game-status', $model->getStatusClass()]]);
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
