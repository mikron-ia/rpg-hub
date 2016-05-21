<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Epic */

$this->title = Yii::t('app', 'TITLE_EPIC_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
