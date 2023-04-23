<?php
/* @var $this yii\web\View */
/* @var $model common\models\Description */
/* @var $showPrivates bool */

$pointInTimeStartExists = isset($model->point_in_time_start_id);
$pointInTimeEndExists = isset($model->point_in_time_end_id);

if ($pointInTimeStartExists || $pointInTimeEndExists) {
    if ($pointInTimeStartExists && $pointInTimeEndExists) {
        $message = Yii::t(
            'app', 'DESCRIPTION_VALID_BOTH {start} {end}',
            ['start' => $model->pointInTimeStart->name, 'end' => $model->pointInTimeEnd->name]
        );
        $position = $model->pointInTimeStart->position; // use start whenever possible
        $displayPointsInTime = true;
    } elseif ($pointInTimeStartExists) {
        $message = Yii::t('app', 'DESCRIPTION_VALID_START {start}', ['start' => $model->pointInTimeStart->name]);
        $position = $model->pointInTimeStart->position;
        $displayPointsInTime = true;
    } elseif ($pointInTimeEndExists) {
        $message = Yii::t('app', 'DESCRIPTION_VALID_END {end}', ['end' => $model->pointInTimeEnd->name]);
        $position = $model->pointInTimeEnd->position;
        $displayPointsInTime = true;
    } else {
        $displayPointsInTime = false;
    }
}

?>

<div class="col-md-6">

    <h2><?= $model->getTypeName(); ?></h2>

    <?php if ($displayPointsInTime): ?>
        <div class="tag-box description-timestamp" data-type="<?= $model->code ?>" data-order="<?= $position ?>">
            <?= $message ?>
        </div>
        <div class="tag-box description-outdated" style="display: none;">
            <?= Yii::t('app', 'DESCRIPTION_REPLACED'); ?>
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
