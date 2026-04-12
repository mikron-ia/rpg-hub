<?php

use common\models\Description;
use yii\web\View;

/* @var $this View */
/* @var $model Description */
/* @var $creatorController string */
/* @var $creatorKey string */

$this->title = Yii::t('app', 'DESCRIPTION_TITLE_CREATE');
?>
<div class="description-create">
    <?= $this->render('_form', [
        'model' => $model,
        'creatorController' => $creatorController,
        'creatorKey' => $creatorKey,
    ]) ?>
</div>
