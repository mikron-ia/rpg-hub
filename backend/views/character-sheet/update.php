<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheet */

$this->title = Yii::t('app', 'CHARACTER_SHEET_TITLE_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>
<div class="character-sheet-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
