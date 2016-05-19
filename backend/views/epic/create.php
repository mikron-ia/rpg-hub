<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Epic */

$this->title = Yii::t('app', 'Create Epic');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Epics'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
