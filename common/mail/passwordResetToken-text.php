<?php

use common\models\User;
use yii\web\View;

/* @var $this View */
/* @var $user User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<?= Yii::t('mail', 'GREETING') ?>,

<?= Yii::t('mail', 'RESET_PASSWORD_BODY') ?>

<?= $resetLink ?>
