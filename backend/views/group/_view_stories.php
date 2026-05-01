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
        'formId' => 'form-group-story-assignment-public',
        'attribute' => 'groupStoryAssignmentChoicesPublic',
    ]) ?>

    <?= $this->render('../group-assignment-story/_view_story_form', [
        'model' => $model,
        'formId' => 'form-group-story-assignment-private',
        'attribute' => 'groupStoryAssignmentChoicesPrivate',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_STORY_LIST_STATE') ?></h3>
    <div id="group-story-assignment-list" data-group-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
