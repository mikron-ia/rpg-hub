<?php

use common\models\Game;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Game */

$this->title = Yii::t('app', 'TITLE_GAME_UPDATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_GAME_INDEX'),
    'url' => ['game/index', 'epic' => $model->epic->key],
];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->basics), 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="game-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
