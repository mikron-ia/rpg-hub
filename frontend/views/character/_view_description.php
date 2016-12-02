<?php
/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $showPrivates bool */
?>

<div class="col-md-6">

    <h4><?= $model->getTypeName(); ?></h4>

    <div>
        <?= $model->getPublicFormatted(); ?>
    </div>

    <?php if ($showPrivates && $model->private_text): ?>
        <div class="private-notes">
            <?= $model->getPrivateFormatted(); ?>
        </div>
    <?php endif; ?>

</div>
