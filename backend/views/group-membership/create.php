<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */

$this->title = Yii::t('app', 'LABEL_ADD');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-membership-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
