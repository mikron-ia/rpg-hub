<?php

use common\models\ImageLink;
use yii\web\View;

/* @var $this View */
/* @var $model ImageLink */

$this->title = Yii::t('app', 'IMAGE_LINK_TITLE_UPDATE');
?>
<div class="image-link-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
