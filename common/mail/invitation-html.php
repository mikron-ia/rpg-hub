<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $invitation common\models\UserInvitation */
/* @var $link string */

?>
<div class="password-reset">
    <p><?= Yii::t('mail', 'GREETING') ?>,</p>

    <p><?= $invitation->message ?></p>

    <p><?= Yii::t('mail', 'USER_INVITATION_BODY_TEXT') ?></p>

    <p><?= Html::a(Html::encode($link), $link) ?></p>

    <p>
        <?= Yii::t(
            'mail',
            'USER_INVITATION_EXPIRATION_WARNING {when}',
            ['when' => date('Y-m-d H:i:s', $invitation->valid_to)]
        ) ?>
    </p>
</div>
