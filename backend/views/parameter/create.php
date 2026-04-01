<?php

use common\models\Parameter;
use yii\web\View;

/* @var $this View */
/* @var $model Parameter */

$this->title = Yii::t('app', 'PARAMETER_TITLE_CREATE');
?>
<div class="parameter-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
