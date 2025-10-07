<?php

use common\models\Scenario;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $model Scenario */

$titleText = '';

?>

<div id="scenario-<?php echo $model->key; ?>" class="index-box" title="<?= $titleText ?>">
    <h3 class="index-box-header-narrow">
        <?= Html::a(
            Html::encode(StringHelper::truncateWords(
                $model->name, Yii::$app->params['indexBoxWordTrimming']['withoutTags']['title'],
                ' (...)',
                false
            )),
            ['view', 'key' => $model->key]
        ); ?>
    </h3>
    <p class="subtitle index-box-subtitle-narrow">
        <?= StringHelper::truncateWords(
            $model->tag_line,
            Yii::$app->params['indexBoxWordTrimming']['withoutTags']['subtitle'],
            ' (...)',
            false
        ) ?>
    </p>
</div>
