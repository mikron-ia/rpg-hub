<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Announcement $model */

$this->title = $model->title;

if (isset($model->epic_id)) {
    $this->params['breadcrumbs'][] = [
        'label' => $model->epic->name,
        'url' => ['epic/front', 'key' => $model->epic->key]
    ];
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ANNOUNCEMENT_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

?>

<div class="announcement-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'ANNOUNCEMENT_BASICS') ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'announcement_id',
                'key',
                [
                    'label' => Yii::t('app', 'LABEL_EPIC'),
                    'value' => $model->epic?->name,
                ],
                [
                    'label' => Yii::t('app', 'ANNOUNCEMENT_VISIBLE_TO'),
                    'value' => $model->visible_from,
                ],
                [
                    'label' => Yii::t('app', 'ANNOUNCEMENT_VISIBLE_FROM'),
                    'value' => $model->visible_to,
                ],
                [
                    'label' => Yii::t('app', 'ANNOUNCEMENT_CREATED_BY'),
                    'value' => $model->createdBy?->username,
                ],
                [
                    'label' => Yii::t('app', 'ANNOUNCEMENT_UPDATED_BY'),
                    'value' => $model->updatedBy?->username,
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

        <div class="text-center">
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
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'ANNOUNCEMENT_TEXT') ?></h2>
        <?= $model->text_ready ?>
    </div>
</div>
