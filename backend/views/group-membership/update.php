<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */

$this->title = Yii::t('app', 'LABEL_UPDATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->group_membership_id, 'url' => ['view', 'id' => $model->group_membership_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-membership-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
