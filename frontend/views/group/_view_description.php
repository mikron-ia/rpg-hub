<?php
/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $showPrivates bool */
?>

<div class="col-md-6">

    <h3><?= $model->getTypeName(); ?></h3>

    <?php if (isset($model->point_in_time_id)): ?>
        <div class="tag-box description-timestamp"
             title="<?= Yii::t(
                 'app',
                 'DESCRIPTION_UPDATED_IC_TITLE {when}',
                 ['when' => $model->pointInTime->name]
             ); ?>"
             data-type="<?= $model->code ?>"
             data-order="<?= $model->pointInTime->position ?>">
            <?= $model->pointInTime->name ?>
        </div>
        <div class="tag-box description-outdated" style="display: none;">
            <?= Yii::t('app', 'DESCRIPTION_OUTDATED'); ?>
        </div>
    <?php endif; ?>

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
