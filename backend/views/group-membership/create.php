<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */
/* @var $charactersForMembership \common\models\Character[] */

$this->title = Yii::t('app', 'LABEL_ADD');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-membership-create">

    <?= $this->render('_form', [
        'model' => $model,
        'charactersForMembership' => $charactersForMembership,
    ]) ?>

</div>
