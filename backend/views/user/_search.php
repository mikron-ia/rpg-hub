<?php

use common\models\Character;
use common\models\core\Visibility;
use common\models\Epic;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserQuery */
/* @var $form yii\widgets\ActiveForm */
/* @var $epic Epic */

?>

<div class="person-search">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']); ?>

    <?php echo $form->field($model, 'username') ?>

    <?php echo $form->field($model, 'email') ?>

    <div class="form-group">
        <?= Html::resetButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
