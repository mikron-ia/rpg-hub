<?php

use common\models\CharacterSheet;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model CharacterSheet */
?>

<div class="col-md-12" id="raw-data" title="<?= Yii::t('app', 'CHARACTER_SHEET_RAW_DATA') ?>">
    <pre class="wrapped-json"><?= Html::encode(json_encode(json_decode($model->data), JSON_PRETTY_PRINT)) ?></pre>
</div>
