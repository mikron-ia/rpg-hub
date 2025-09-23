<?php

use common\models\Scenario;
use yii\web\View;

/* @var $this View */
/* @var $model Scenario */

?>

<div>
    <?php if (!empty($model->content)): ?>
        <div class="col-md-12">
            <?= $model->getContentFormatted() ?>
        </div>
    <?php else: ?>
        <p class="no-data-box"><?= Yii::t('app', 'SCENARIO_CONTENT_NOT_AVAILABLE') ?></p>
    <?php endif; ?>
</div>
