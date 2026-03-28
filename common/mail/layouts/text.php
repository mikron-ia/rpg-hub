<?php

use yii\mail\MessageInterface;
use yii\web\View;

/* @var $this View view component instance */
/* @var $message MessageInterface the message being composed */
/* @var $content string main view render result */

?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
--
<?= Yii::t('mail', 'SIGNATURE_TEXT') ?>
<?php echo Yii::$app->params['baseUriForMail'] ?? '' ?>
<?php $this->endPage() ?>
