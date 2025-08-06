<?php

/* @var $this yii\web\View */
/* @var $model common\models\Parameter */

$this->title = Yii::t('app', 'PARAMETER_TITLE_UPDATE') . ': ' . $model->parameter_id;
?>
<div class="parameter-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
