<?php

use common\models\user\UserAcceptForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model UserAcceptForm */

$this->title = Yii::t('app', 'USER_ACCOUNT_CREATE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'USER_ACCEPT_WARNING') ?></p>
    <p><?= Yii::t('app', 'USER_ACCEPT_TLDR') ?></p>
    <p>
        <?= Yii::t(
            'app',
            'USER_ACCEPT_EXPIRATION_WARNING {when}',
            ['when' => date('Y-m-d H:i:s', $model->getExpirationTime())]
        ) ?>
    </p>

    <?= $this->render('_form_accept', [
        'model' => $model,
    ]) ?>

</div>
