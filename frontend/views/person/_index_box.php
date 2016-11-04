<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $model \common\models\Person */

?>

<div id="person-<?php echo $model->person_id; ?>">

    <h3 class="center">
        <?= Html::a(
            Html::encode(StringHelper::truncateWords($model->name, 16, ' (...)', false)),
            ['view', 'id' => $model->person_id]
        ); ?>
    </h3>

    <p class="subtitle">
        <?= StringHelper::truncateWords($model->tagline, 16, ' (...)', false) ?>
    </p>

</div>
