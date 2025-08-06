<?php

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */
/* @var $charactersForMembership \common\models\Character[] */

$this->title = Yii::t('app', 'LABEL_ADD');
?>
<div class="group-membership-create">

    <?= $this->render('_form', [
        'model' => $model,
        'charactersForMembership' => $charactersForMembership,
    ]) ?>

</div>
