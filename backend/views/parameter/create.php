<?php

use common\models\Parameter;
use yii\web\View;

/* @var $this View */
/* @var $model Parameter */
/* @var $creatorController string */
/* @var $creatorKey string */

$this->title = Yii::t('app', 'PARAMETER_TITLE_CREATE');
?>
<div class="parameter-create">
    <?= $this->render('_form', [
        'model' => $model,
        'creatorController' => $creatorController,
        'creatorKey' => $creatorKey,
    ]) ?>
</div>
