<?php

use common\models\Character;
use common\models\GroupMembership;
use yii\web\View;

/* @var $this View */
/* @var $model GroupMembership */
/* @var $charactersForMembership Character[] */

$this->title = Yii::t('app', 'LABEL_ADD');
?>
<div class="group-membership-create">
    <?= $this->render('_form', [
        'model' => $model,
        'charactersForMembership' => $charactersForMembership,
    ]) ?>
</div>
