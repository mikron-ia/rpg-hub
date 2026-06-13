<?php

use kartik\select2\Select2;
use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model Model */
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
            'data-object-class' => 'Secret',
        ],
        'pluginOptions' => ['allowClear' => true],
    ],
); ?>
<?php ActiveForm::end(); ?>
