<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Recap */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'RECAP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recap-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-right">
        <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->recap_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->recap_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'QUESTION_DELETE'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <p><b><?= $model->getAttributeLabel('key'); ?>:</b> <?= $model->key; ?></p>

    <p><b><?= $model->getAttributeLabel('time'); ?>:</b> <?= $model->time; ?></p>

    <p>
        <b><?= $model->getAttributeLabel('epic_id'); ?>:</b>
        <?= Html::a(
            $model->epic->name,
            ['epic/view', 'id' => $model->epic_id],
            []
        ); ?>
    </p>

    <h2><?= Yii::t('app', 'LABEL_CONTENT'); ?></h2>
    <div>
        <?= $model->getDataFormatted(); ?>
    </div>

</div>
