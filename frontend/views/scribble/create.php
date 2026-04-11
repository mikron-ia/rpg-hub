<?php

use common\models\Scribble;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var Scribble $model */

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
