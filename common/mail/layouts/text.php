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
<?php if (isset(Yii::$app->params['baseUriForMail'])): ?>
    <?= Yii::t('mail', 'SIGNATURE_TEXT_WITH_LINK', ['link' => Yii::$app->params['baseUriForMail']]) ?>
<?php else: ?>
    <?= Yii::t('mail', 'SIGNATURE_TEXT_SIMPLE') ?>
<?php endif ?>
<?php $this->endPage() ?>
