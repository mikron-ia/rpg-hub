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
        'formId' => 'form-story-character-assignment-public',
        'attribute' => 'storyCharacterAssignmentChoicesPublic',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-character-assignment-private',
        'attribute' => 'storyCharacterAssignmentChoicesPrivate',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_CHARACTER_LIST_STATE') ?></h3>
    <div id="story-character-assignment-list" data-story-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
