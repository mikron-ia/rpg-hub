<?php

/* @var $this yii\web\View */
/* @var $model common\models\Parameter */

$this->title = Yii::t('app', 'PARAMETER_TITLE_CREATE');
?>
<div class="parameter-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
