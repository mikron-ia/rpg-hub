<?php

use common\models\Parameter;
use yii\web\View;

/* @var $this View */
/* @var $model Parameter */

$this->title = Yii::t('app', 'PARAMETER_TITLE_UPDATE') . ': ' . $model->getTypeName();
?>
<div class="parameter-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
