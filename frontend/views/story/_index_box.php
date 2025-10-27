<?php

use common\models\Parameter;
use yii\helpers\Html;

/** @var $model \common\models\Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);

if ($storyNumberRaw) {
    $storyNumber = $storyNumberRaw . ' ';
} else {
    $storyNumber = '';
}

?>

<div id="story-<?php echo $model->story_id; ?>">
    <h4>
        <?= Html::a(
            $model->epic->name,
            ['epic/view', 'key' => $model->epic->key]
        ) ?> / <?= Html::a(
            Html::encode($storyNumber . $model->name),
            ['story/view', 'key' => $model->key]
        ); ?>
        <?php if (!empty($model->hasCodeName())): ?>
            <span class="text-center type-tag tag-smaller"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
    </h4>
    <div class="col-md-12 text-justify">
        <?php echo $model->getShortFormatted(); ?>
    </div>
</div>

<div class="clearfix"></div>