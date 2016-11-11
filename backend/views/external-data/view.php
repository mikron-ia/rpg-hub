<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ExternalData */

$this->title = $model->external_data_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'EXTERNAL_DATA_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-data-view">

    <div class="buttoned-header">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->external_data_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                'method' => 'post',
            ],
        ]) ?>

    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'external_data_id',
            'external_data_pack_id',
            'externalDataPack.class',
            'code',
            'data:ntext',
            'visibility',
        ],
    ]) ?>

</div>
