<?php

use common\models\core\HasDescriptions;

/* @var $model HasDescriptions */
?>

<?php if ($model->getDescriptionPackId()): ?>
    <div id="description-container" data-pack-id="<?= $model->getDescriptionPackId() ?>">
        <div class="circle-loader"></div>
    </div>
<?php endif; ?>