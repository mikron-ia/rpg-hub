<?php

use common\models\Image;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var View $this */
/** @var Image $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'IMAGE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="image-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'key' => $model->key],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'key' => $model->key],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key]),
                ],
                'key',
                'name',
                'display_height',
                'display_width',
                'created_at:datetime',
                'updated_at:datetime',
                [
                    'attribute' => 'created_by',
                    'value' => $model->createdBy?->username,
                ],
                [
                    'attribute' => 'updated_by',
                    'value' => $model->updatedBy?->username,
                ],
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <pre>IMAGE PLACEHOLDER</pre>
    </div>
    <div class="col-md-6">
        <h3 class="text-center"><?= Yii::t('app', 'IMAGE_TITLE') ?></h3>
        <div><?= $model->title ?></div>
        <h3 class="text-center"><?= Yii::t('app', 'IMAGE_ALT') ?></h3>
        <div><?= $model->alt ?></div>
    </div>
    <div class="col-md-6">
        <h3 class="text-center"><?= Yii::t('app', 'IMAGE_NOTE') ?></h3>
        <div><?= $model->note ?></div>
    </div>
</div>
