<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Character */

$this->title = Yii::t('app', 'TITLE_CHARACTER_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_CHARACTER_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
