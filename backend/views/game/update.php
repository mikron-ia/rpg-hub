<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Game */

$this->title = Yii::t('app', 'TITLE_GAME_UPDATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_GAME_INDEX'),
    'url' => ['game/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = ['label' => $model->game_id, 'url' => ['view', 'id' => $model->game_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="game-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
