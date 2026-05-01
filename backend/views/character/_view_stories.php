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
        'formId' => 'form-character-story-assignment-public',
        'attribute' => 'characterStoryAssignmentChoicesPublic',
    ]) ?>

    <?= $this->render('../character-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-character-story-assignment-private',
        'attribute' => 'characterStoryAssignmentChoicesPrivate',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_STATE') ?></h3>
    <div id="character-story-assignment-list" data-character-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
