<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'USER_ACCOUNT_CREATE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'USER_ACCEPT_WARNING') ?></p>
    <p><?= Yii::t('app', 'USER_ACCEPT_TLDR') ?></p>

    <?= $this->render('_form_accept', [
        'model' => $model,
    ]) ?>

</div>
