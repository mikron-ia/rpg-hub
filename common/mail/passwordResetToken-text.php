<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<?= Yii::t('mail', 'GREETING') ?>,

<?= Yii::t('mail', 'RESET_PASSWORD_BODY') ?>

<?= $resetLink ?>
