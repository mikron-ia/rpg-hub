<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Scribble $model */

$this->title = 'Create Scribble';
$this->params['breadcrumbs'][] = ['label' => 'Scribbles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scribble-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
