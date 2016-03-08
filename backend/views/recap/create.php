<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Recap */

$this->title = Yii::t('app', 'Create Recap');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Recaps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recap-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
