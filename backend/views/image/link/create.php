<?php

use common\models\ImageLink;
use yii\web\View;

/* @var $this View */
/* @var $model ImageLink */

$this->title = Yii::t('app', 'IMAGE_LINK_TITLE_CREATE');
?>
<div class="image-link-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
