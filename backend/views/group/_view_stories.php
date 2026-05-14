<?php

use common\models\Group;
use common\models\StoryQuery;
use yii\web\View;

/* @var $this View */
/* @var $model Group */

$storiesForDropdown = StoryQuery::listEpicStoriesAsArrayForDropdown($model->epic);
$viewFile = '../group-assignment-story/_view_story_form';
?>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_CONFIGURATION') ?></h3>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
        'formId' => 'form-group-story-assignment-public-vital',
        'attribute' => 'groupStoryAssignmentChoicesPublicVital',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
        'formId' => 'form-group-story-assignment-public-major',
        'attribute' => 'groupStoryAssignmentChoicesPublicMajor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
        'formId' => 'form-group-story-assignment-public-minor',
        'attribute' => 'groupStoryAssignmentChoicesPublicMinor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
        'formId' => 'form-group-story-assignment-public-other',
        'attribute' => 'groupStoryAssignmentChoicesPublicOther',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
        'formId' => 'form-group-story-assignment-private-vital',
        'attribute' => 'groupStoryAssignmentChoicesPrivateVital',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
        'formId' => 'form-group-story-assignment-private-major',
        'attribute' => 'groupStoryAssignmentChoicesPrivateMajor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
        'formId' => 'form-group-story-assignment-private-minor',
        'attribute' => 'groupStoryAssignmentChoicesPrivateMinor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'storiesForDropdown' => $storiesForDropdown,
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
