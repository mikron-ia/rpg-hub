<?php

use common\models\Group;
use yii\web\View;

/* @var $this View */
/* @var $model Group */
?>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_CONFIGURATION') ?></h3>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-public-vital',
        'attribute' => 'groupStoryAssignmentChoicesPublicVital',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-public-major',
        'attribute' => 'groupStoryAssignmentChoicesPublicMajor',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-public-minor',
        'attribute' => 'groupStoryAssignmentChoicesPublicMinor',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-public-other',
        'attribute' => 'groupStoryAssignmentChoicesPublicOther',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-private-vital',
        'attribute' => 'groupStoryAssignmentChoicesPrivateVital',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-private-major',
        'attribute' => 'groupStoryAssignmentChoicesPrivateMajor',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-private-minor',
        'attribute' => 'groupStoryAssignmentChoicesPrivateMinor',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-private-other',
        'attribute' => 'groupStoryAssignmentChoicesPrivateOther',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_STATE') ?></h3>
    <div id="group-story-assignment-list" data-group-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
