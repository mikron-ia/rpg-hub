<?php

/* @var $this yii\web\View */
/* @var $model common\models\Parameter */

$this->title = Yii::t('app', 'PARAMETER_TITLE_UPDATE') . ': ' . $model->parameter_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PARAMETER_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->getTypeName();
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="parameter-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
