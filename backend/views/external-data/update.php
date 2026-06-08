<?php

use common\models\ExternalData;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model ExternalData */

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
