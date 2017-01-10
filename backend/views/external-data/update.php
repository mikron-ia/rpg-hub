<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ExternalData */

$this->title = Yii::t('app', 'EXTERNAL_DATA_UPDATE_TITLE');
$this->params['breadcrumbs'][] = [
    'label' => $model->external_data_id,
    'url' => ['view', 'id' => $model->external_data_id]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="external-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
