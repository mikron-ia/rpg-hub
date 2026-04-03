<?php

use common\models\core\HasDescriptions;

/* @var $model HasDescriptions */
?>

<?php if ($model->getDescriptionPackKey()): ?>
    <div id="description-container" data-pack-key="<?= $model->getDescriptionPackKey() ?>">
        <div class="circle-loader"></div>
    </div>
<?php endif; ?>