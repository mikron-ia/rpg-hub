<?php

use common\models\Scribble;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var Scribble $model */

$this->title = 'Update Scribble: ' . $model->scribble_id;
$this->params['breadcrumbs'][] = ['label' => 'Scribbles', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->scribble_id,
    'url' => ['view', 'scribble_id' => $model->scribble_id],
];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="scribble-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
