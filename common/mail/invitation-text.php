<?php

/* @var $this yii\web\View */
/* @var $invitation common\models\UserInvitation */

$link = Yii::$app->urlManager->createAbsoluteUrl(['user/accept', 'token' => $invitation->token]);
?>
<?= Yii::t('mail', 'GREETING') ?>,

<?= $invitation->message ?>

<?= Yii::t('mail', 'USER_INVITATION_BODY_TEXT') ?>

<?= $link ?>
