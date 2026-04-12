<?php

use common\models\core\HasDescriptions;

/* @var $model HasDescriptions */
?>

<?php if ($model->getObjectKey()): ?>
    <div id="description-container" data-object-key="<?= $model->getObjectKey() ?>">
        <div class="circle-loader"></div>
    </div>
<?php endif; ?>
