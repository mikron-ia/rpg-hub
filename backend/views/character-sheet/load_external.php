<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheet */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->character_sheet_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'EXTERNAL_DATA_TITLE {name}', ['name' => $model->name]);
?>

<div class="external-data-form">

    <h1><?= Yii::t('app', 'EXTERNAL_DATA_TITLE {name}', ['name' => $model->name]) ?></h1>

    <p class="info-box"><?= Yii::t('app', 'EXTERNAL_DATA_INSTRUCTION') ?></p>

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-12">
        <?= Html::textarea(
            'external-data',
            '',
            [
                'placeholder' => Yii::t('app', 'CHARACTER_SHEET_EXTERNAL_DATA_PLACEHOLDER'),
                'rows' => 20,
                'class' => 'external-data-textarea'
            ]
        ) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'BUTTON_EXTRACT'),
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
