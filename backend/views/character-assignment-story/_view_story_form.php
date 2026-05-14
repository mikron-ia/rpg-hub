<?php

use common\models\Character;
use common\models\StoryQuery;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model Character */
/* @var $formId string */
/* @var $attribute string */
/* @var $storiesForDropdown array<int,string> */

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
        'data' => $storiesForDropdown,
        'options' => ['multiple' => true, 'data-character-key' => $model->key],
        'pluginOptions' => ['allowClear' => true],
    ],
); ?>
<?php ActiveForm::end(); ?>
