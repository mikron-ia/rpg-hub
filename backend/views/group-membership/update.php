<?php

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */
/* @var $charactersForMembership \common\models\Character[] */

$this->title = Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_UPDATE {name}', ['name' => $model->character->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->group_membership_id,
    'url' => ['view', 'id' => $model->group_membership_id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-membership-update">

    <?= $this->render('_form', [
        'model' => $model,
        'charactersForMembership' => $charactersForMembership,
    ]) ?>

    <?php $this->registerJs("$('#membership-update-modal-title').html('" . $this->title . "');"); ?>

</div>
