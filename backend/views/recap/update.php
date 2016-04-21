<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Recap */

$this->title = Yii::t('app', 'RECAP_TITLE_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'RECAP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->recap_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BUTTON_UPDATE');
?>
<div class="recap-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
