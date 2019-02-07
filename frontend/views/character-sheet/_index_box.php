<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $model \common\models\CharacterSheet */

?>

<div id="character-sheet-<?php echo $model->key; ?>" class="index-box index-box-designated">

    <h3 class="center">
        <?= Html::a(
            Html::encode(StringHelper::truncateWords($model->name, 16, ' (...)', false)),
            ['view', 'key' => $model->key]
        ); ?>
    </h3>

    <p class="text-center seen-tag-common <?= $model->showSightingCSS() ?> seen-tag-box">
        <?= $model->showSightingStatus() ?>
    </p>

</div>
