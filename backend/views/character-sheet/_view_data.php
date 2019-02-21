<?php
/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheet */
?>

<div class="col-md-12">
    <textarea id="raw-data" title="<?= Yii::t('app', 'CHARACTER_SHEET_RAW_DATA') ?>"
              class="col-md-12" rows="28"
              readonly="readonly"
    ><?= \yii\helpers\Html::encode($model->data); ?></textarea>
</div>
