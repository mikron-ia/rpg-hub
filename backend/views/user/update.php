<?php

use common\models\User;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model User */

$this->title = Yii::t('app', 'USER_UPDATE_TITLE {user_name}', ['user_name' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'USER_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_update', [
        'model' => $model,
    ]) ?>

</div>
