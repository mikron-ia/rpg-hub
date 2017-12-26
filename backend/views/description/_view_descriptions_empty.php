<?php

/* @var $model common\models\Character */

?>

<?php if ($model->description_pack_id): ?>
    <div id="description-container" data-pack-id="<?= $model->description_pack_id ?>">
        <div class="circle-loader"></div>
    </div>
<?php endif; ?>