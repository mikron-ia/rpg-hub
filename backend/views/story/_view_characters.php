<?php

use common\models\Story;
use yii\web\View;

/* @var $this View */
/* @var $model Story */

$viewFile = '../story-assignment-character/_view_character_form';
?>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_CHARACTER_LIST_CONFIGURATION') ?></h3>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-public-vital',
        'attribute' => 'storyCharacterAssignmentChoicesPublicVital',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-public-major',
        'attribute' => 'storyCharacterAssignmentChoicesPublicMajor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-public-minor',
        'attribute' => 'storyCharacterAssignmentChoicesPublicMinor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-public-other',
        'attribute' => 'storyCharacterAssignmentChoicesPublicOther',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-private-vital',
        'attribute' => 'storyCharacterAssignmentChoicesPrivateVital',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-private-major',
        'attribute' => 'storyCharacterAssignmentChoicesPrivateMajor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-private-minor',
        'attribute' => 'storyCharacterAssignmentChoicesPrivateMinor',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-private-other',
        'attribute' => 'storyCharacterAssignmentChoicesPrivateOther',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_CHARACTER_LIST_STATE') ?></h3>
    <div id="story-character-assignment-list" data-story-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
