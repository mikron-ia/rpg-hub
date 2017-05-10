<?php
/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $showPrivates bool */
?>

<div class="col-md-6">

    <h3><?= $model->getTypeName(); ?></h3>

    <div>
        <?= $model->getPublicFormatted(); ?>
    </div>

    <?php if ($showPrivates && $model->private_text): ?>
        <div class="private-notes secret">
            <?= $model->getPrivateFormatted(); ?>
        </div>
    <?php endif; ?>

</div>
