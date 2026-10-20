<?php

use common\models\core\HasParameters;
use common\models\Parameter;
use yii\web\View;

/* @var $this View */
/* @var $model HasParameters */

$effectList = [];
$parameterNames = Parameter::typeNames();
foreach (Parameter::effectDescriptions() as $key => $description) {
    if(in_array($key, $model->availableParameterTypes())) {
        $effectList[$parameterNames[$key]] = $description;
    }
}
?>

<?php if($effectList): ?>
    <div>
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
    </div>
<?php else: ?>
    <div>
        <?= Yii::t('app', 'PARAM_EFFECT_NONE') ?>
    </div>
<?php endif; ?>
