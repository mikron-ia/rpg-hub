<?php

use common\models\Epic;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Epic */

$this->title = Yii::t('app', 'TITLE_EPIC_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="info-box"><?= Yii::t('app', 'EPIC_CREATION_WARNING'); ?></p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
