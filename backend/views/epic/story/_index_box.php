<?php

use common\models\Parameter;
use common\models\Story;
use yii\helpers\Html;

/** @var $model Story */

$storyNumberRaw = $model->getParameter(Parameter::STORY_NUMBER);
$storyNumber = $storyNumberRaw ? $storyNumberRaw . ' ' : '';

?>

<div id="story-<?php echo $model->story_id; ?>">
    <h4 class="center">
        <?php echo Html::a(Html::encode($storyNumber . $model->name), ['story/view', 'key' => $model->key]); ?>
    </h4>
</div>

<div class="clearfix"></div>