<?php
/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $showPrivates bool */
?>

<div>

    <h3><?= Yii::t('app', 'GROUP_MEMBERSHIPS_ACTIVE'); ?></h3>
    <div id="members-active">
        <?= $this->render('_view_composition', [
            'models' => $model->groupCharacterMembershipsActive
        ]) ?>
    </div>

    <h3><?= Yii::t('app', 'GROUP_MEMBERSHIPS_PASSIVE'); ?></h3>
    <div id="members-passive">
        <?= $this->render('_view_composition', [
            'models' => $model->groupCharacterMembershipsPassive
        ]) ?>
    </div>

    <h3><?= Yii::t('app', 'GROUP_MEMBERSHIPS_PAST'); ?></h3>
    <div id="members-past">
        <?= $this->render('_view_composition', [
            'models' => $model->groupCharacterMembershipsPast
        ]) ?>
    </div>

</div>
