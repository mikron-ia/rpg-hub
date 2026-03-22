<?php

use common\models\Description;
use yii\web\View;

/* @var $this View */
/* @var $model Description */

$this->title = Yii::t('app', 'DESCRIPTION_TITLE_UPDATE');
?>
<div class="description-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
