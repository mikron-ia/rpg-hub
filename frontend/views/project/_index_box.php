<?php

use common\models\Project;
use yii\helpers\Html;

/** @var $model Project */

?>

<div id="project-<?= $model->project_id ?>">
    <h4>
        <?= Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key]) ?> / <?= Html::a(
            Html::encode($model->name),
            ['project/view', 'key' => $model->key]
        ); ?>
        <?php if ($model->displayCodeName()): ?>
            <span class="text-center type-tag tag-smaller"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
    </h4>
    <div class="col-md-12 text-justify">
        <?= $model->getShortFormatted() ?>
    </div>
</div>

<div class="clearfix"></div>
