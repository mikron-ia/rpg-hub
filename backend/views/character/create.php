<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Character */

$this->title = Yii::t('app', 'CHARACTER_TITLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CHARACTER_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
