<?php

use common\models\User;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $user User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p><?= Yii::t('mail', 'GREETING') ?>,</p>

    <p><?= Yii::t('mail', 'RESET_PASSWORD_BODY') ?></p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
