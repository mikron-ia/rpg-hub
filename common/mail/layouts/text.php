<?php

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
--
<?= Yii::t('mail', 'SIGNATURE_TEXT') ?>
<?php echo isset(Yii::$app->params['baseUriForMail']) ? Yii::$app->params['baseUriForMail'] : '' ?>
<?php $this->endPage() ?>
