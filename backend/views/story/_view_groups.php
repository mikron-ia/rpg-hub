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
        'formId' => 'form-story-group-assignment-public',
        'attribute' => 'storyGroupAssignmentChoicesPublic',
    ]) ?>

    <?= $this->render($viewFile, [
        'model' => $model,
        'formId' => 'form-story-group-assignment-private',
        'attribute' => 'storyGroupAssignmentChoicesPrivate',
    ]) ?>
</div>

<div class="col-md-6">
    <h3 class="text-center"><?= Yii::t('app', 'LABEL_GROUP_LIST_STATE') ?></h3>
    <div id="story-group-assignment-list" data-story-key="<?= $model->key ?>">
        <div class="circle-loader"></div>
    </div>
</div>
