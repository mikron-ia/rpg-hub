<?php

/* @var $this yii\web\View */
/* @var $invitation common\models\UserInvitation */
/* @var $link string */

?>
<?= Yii::t('mail', 'GREETING') ?>,

<?= $invitation->message ?>

<?= Yii::t('mail', 'USER_INVITATION_BODY_TEXT') ?>

<?= $link ?>
