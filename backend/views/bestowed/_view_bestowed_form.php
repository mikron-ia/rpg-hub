<?php

use kartik\select2\Select2;
use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model Model */
/* @var $class string */
/* @var $formId string */
/* @var $listKey string */
/* @var $attribute string */
/* @var $usersForDropdown array<int,string> */

$formTemplate = '{label}
<div class="input-group assignment-input-group">
    {input}
    <span class="input-group-btn">'
    . Html::submitButton(Yii::t('app', 'BUTTON_SAVE'), ['class' => 'btn btn-primary'])
    . '</span>
</div>
{hint}
{error}
';
?>

<?php $form = ActiveForm::begin(['id' => $formId]); ?>
<?= $form->field($model, $attribute, ['template' => $formTemplate])->widget(
    Select2::class,
    [
        'data' => $usersForDropdown,
        'options' => [
            'multiple' => true,
            'data-list-key' => $listKey,
            'data-object-class' => $class,
        ],
        'pluginOptions' => ['allowClear' => true],
    ],
); ?>
<?php ActiveForm::end(); ?>

<div style="display: none;" id="form-bestow-access-success" class="alert alert-success">
    <?= Yii::t('app', 'BESTOWED_FORM_SUCCES') ?>
</div>

<div style="display: none;" id="form-bestow-access-fail" class="alert alert-danger">
    <?= Yii::t('app', 'BESTOWED_FORM_ERROR') ?>: <span id="form-bestow-access-fail-text"></span>
</div>
