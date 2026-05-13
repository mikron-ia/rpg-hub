<?php

use common\models\Character;
use yii\web\View;

/* @var $this View */
/* @var $model Character */
?>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_CONFIGURATION') ?></h3>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-public-vital',
        'attribute' => 'characterStoryAssignmentChoicesPublicVital',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-public-major',
        'attribute' => 'characterStoryAssignmentChoicesPublicMajor',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-public-minor',
        'attribute' => 'characterStoryAssignmentChoicesPublicMinor',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-public-other',
        'attribute' => 'characterStoryAssignmentChoicesPublicOther',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-private-vital',
        'attribute' => 'characterStoryAssignmentChoicesPrivateVital',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-private-major',
        'attribute' => 'characterStoryAssignmentChoicesPrivateMajor',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-private-minor',
        'attribute' => 'characterStoryAssignmentChoicesPrivateMinor',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-private-other',
        'attribute' => 'characterStoryAssignmentChoicesPrivateOther',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_STATE') ?></h3>
    <div id="character-story-assignment-list" data-character-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
