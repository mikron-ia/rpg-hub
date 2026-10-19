<?php

use common\models\Parameter;
use common\models\Story;
use yii\web\View;

/* @var $this View */
/* @var $model Story */

$effectList = [];
$parameterNames = Parameter::typeNames();
foreach (Parameter::effectDescriptions() as $key => $description) {
    if(in_array($key, Story::availableParameterTypes())) {
        $effectList[$parameterNames[$key]] = $description;
    }
}
?>

<div>
    <?= Yii::t('app', 'PARAM_EFFECT_EXPLANATION') ?>
</div>

<ul>
    <?php foreach ($effectList as $name => $description): ?>
        <li><strong><?= $name ?>:</strong> <?= $description ?></li>
    <?php endforeach; ?>
</ul>

<div>
    <?= Yii::t('app', 'PARAM_EFFECT_OTHER') ?>
</div>
