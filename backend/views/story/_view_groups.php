<?php

use common\models\Story;
use yii\web\View;

/* @var $this View */
/* @var $model Story */

$viewFile = '../story-assignment-group/_view_group_form';
?>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_GROUP_LIST_CONFIGURATION') ?></h3>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-public-vital',
        'attribute' => 'storyGroupAssignmentChoicesPublicVital',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-public-major',
        'attribute' => 'storyGroupAssignmentChoicesPublicMajor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-public-minor',
        'attribute' => 'storyGroupAssignmentChoicesPublicMinor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-public-other',
        'attribute' => 'storyGroupAssignmentChoicesPublicOther',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-private-vital',
        'attribute' => 'storyGroupAssignmentChoicesPrivateVital',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-private-major',
        'attribute' => 'storyGroupAssignmentChoicesPrivateMajor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-private-minor',
        'attribute' => 'storyGroupAssignmentChoicesPrivateMinor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-private-other',
        'attribute' => 'storyGroupAssignmentChoicesPrivateOther',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_GROUP_LIST_STATE') ?></h3>
    <div id="story-group-assignment-list" data-story-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
