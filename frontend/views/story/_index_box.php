<?php

use common\models\Parameter;
use common\models\Story;
use yii\helpers\Html;

/** @var $model Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);

?>

<div id="story-<?= $model->story_id ?>">
    <h4>
        <?= Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key]) ?> / <?= Html::a(
            Html::encode((empty($storyNumberRaw) ? '' : $storyNumberRaw . ' ') . $model->name),
            ['story/view', 'key' => $model->key]
        ); ?>
        <?php if ($model->hasCodeName()): ?>
            <span class="text-center type-tag tag-smaller"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
    </h4>
    <div class="col-md-12 text-justify">
        <?= $model->getShortFormatted() ?>
    </div>
</div>

<div class="clearfix"></div>
