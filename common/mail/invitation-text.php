<?php

/* @var $this yii\web\View */
/* @var $invitation common\models\UserInvitation */
/* @var $link string */

?>
<?= Yii::t('mail', 'GREETING') ?>,

<?= $invitation->message ?>

<?= Yii::t('mail', 'USER_INVITATION_BODY_TEXT') ?>

<?= $link ?>

<?= Yii::t(
    'mail',
    'USER_INVITATION_EXPIRATION_WARNING {when}',
    ['when' => date('Y-m-d H:i:s', $invitation->valid_to)]
) ?>