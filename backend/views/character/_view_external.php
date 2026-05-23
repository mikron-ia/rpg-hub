<?php

use common\models\Character;
use yii\web\View;

/* @var $this View */
/* @var $model Character */
?>

<div class="col-md-12">
    <pre><?= json_encode(json_decode($model->data), JSON_PRETTY_PRINT) ?></pre>
</div>
