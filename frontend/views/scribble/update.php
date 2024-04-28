<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Scribble $model */

$this->title = 'Update Scribble: ' . $model->scribble_id;
$this->params['breadcrumbs'][] = ['label' => 'Scribbles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->scribble_id, 'url' => ['view', 'scribble_id' => $model->scribble_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="scribble-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
