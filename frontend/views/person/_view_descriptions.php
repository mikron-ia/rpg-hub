<?php
/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $showPrivates bool */
?>

<div class="col-lg-6">

    <h3><?= $model->getTypeName(); ?></h3>

    <div>
        <?= $model->getPublicFormatted(); ?>
    </div>

    <?php if ($showPrivates && $model->private_text): ?>
        <h4><?= Yii::t('app', 'DESCRIPTION_TITLE_PRIVATE'); ?></h4>

        <div>
            <?= $model->getPrivateFormatted(); ?>
        </div>
    <?php endif; ?>

</div>
