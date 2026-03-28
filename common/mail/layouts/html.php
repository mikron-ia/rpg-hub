<?php

use yii\helpers\Html;
use yii\mail\MessageInterface;
use yii\web\View;

/* @var $this View view component instance */
/* @var $message MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
--
<?= Yii::t('mail', 'SIGNATURE_HTML') ?>
<?php echo isset(Yii::$app->params['baseUriForMail'])
    ? Html::a(Yii::$app->params['baseUriForMail'], Yii::$app->params['baseUriForMail'])
    : '' ?>
</body>
</html>
<?php $this->endPage() ?>
