<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Epic */

$this->title = Yii::t('app', 'TITLE_UPDATE: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_INDEX_EPIC'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->epic_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="epic-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
