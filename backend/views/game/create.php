<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Game */

$this->title = Yii::t('app', 'TITLE_GAME_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_GAME_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
