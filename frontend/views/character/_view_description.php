<?php
/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $showPrivates bool */
?>

<div class="col-md-6">

    <h2><?= $model->getTypeName(); ?></h2>

    <div class="public-notes">
        <?= $model->getPublicFormatted(); ?>
    </div>

    <?php if ($model->protected_text): ?>
        <div class="protected-notes comment">
            <?= $model->getProtectedFormatted(); ?>
        </div>
    <?php endif; ?>

    <?php if ($showPrivates && $model->private_text): ?>
        <div class="private-notes secret">
            <?= $model->getPrivateFormatted(); ?>
        </div>
    <?php endif; ?>

</div>
