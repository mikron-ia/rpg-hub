<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Person */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'People'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->person_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->person_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <p class="note"><?= $model->tagline; ?></p>

    <p><b><?= $model->getAttributeLabel('key'); ?>:</b> <?= $model->key; ?></p>

    <p>
        <b><?= $model->getAttributeLabel('epic_id'); ?>:</b>
        <?= Html::a(
            $model->epic->name,
            ['epic/view', 'id' => $model->epic_id],
            []
        ); ?>
    </p>

    <p>
        <b><?= $model->getAttributeLabel('character_id'); ?>:</b>
        <?php
        echo $model->character_id ?
            Html::a($model->character->name, ['epic/view', 'id' => $model->character_id], []) :
            Yii::t('app', 'CHARACTER_FIELD_NOT_SET');
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'data:ntext',
            'visibility',
        ],
    ]) ?>

</div>
