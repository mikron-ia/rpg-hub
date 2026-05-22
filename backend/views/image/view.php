<?php

use backend\assets\ImageAsset;
use common\models\Image;
use common\models\ImageLink;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Image */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'IMAGE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

ImageAsset::register($this);
?>
<div class="image-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'key' => $model->key],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= count($model->imageLinks) === 0 ? Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'key' => $model->key],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]
        ) : '' ?>
    </div>

    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <div class="clearfix"></div>

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

    <div class="col-md-6" id="key-div">
        <h3 class="text-center"><?= Yii::t('app', 'IMAGE_KEY'); ?></h3>
        <p class="key"><?= $model->key ?></p>
    </div>

    <div class="col-md-6">
        <h3 class="text-center"><?= Yii::t('app', 'IMAGE_TITLE') ?></h3>
        <?php if ($model->title) : ?>
            <div><?= $model->title ?></div>
        <?php else : ?>
            <div class="no-data-box"><?= Yii::t('app', 'IMAGE_TITLE_EMPTY') ?></div>
        <?php endif; ?>

        <h3 class="text-center"><?= Yii::t('app', 'IMAGE_ALT') ?></h3>
        <?php if ($model->alt) : ?>
            <div><?= $model->alt ?></div>
        <?php else : ?>
            <div class="no-data-box"><?= Yii::t('app', 'IMAGE_ALT_EMPTY') ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h3 class="text-center"><?= Yii::t('app', 'IMAGE_NOTE') ?></h3>
        <?php if ($model->note) : ?>
            <div><?= $model->getNoteFormatted() ?></div>
        <?php else : ?>
            <div class="no-data-box"><?= Yii::t('app', 'IMAGE_NOTE_EMPTY') ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-12">
        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'IMAGE_LINK_TITLE_INDEX') ?></h2>
            <?= Html::a(
                '<span class="btn btn-success">' . Yii::t('app', 'BUTTON_IMAGE_LINK_CREATE') . '</span>',
                '#',
                [
                    'class' => 'create-image-link',
                    'title' => Yii::t('app', 'BUTTON_IMAGE_LINK_CREATE'),
                    'data-controller' => 'epic',
                    'data-key' => $model->key,
                    'data-toggle' => 'modal',
                    'data-target' => '#create-image-link-modal'
                ]
            ); ?>
        </div>
        <p class="beta-feature-warning" title="<?= Yii::t('app', 'ALPHA_WARNING_TITLE') ?>">
            <?= Yii::t('app', 'ALPHA_WARNING_TEXT') ?>
        </p>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => ImageLink::find()->where(['image_id' => $model->image_id]),
                'pagination' => false,
            ]),
            'emptyText' => Yii::t('app', 'IMAGE_LINK_EMPTY_LIST'),
            'summary' => '',
            'options' => ['style' => 'table-layout: fixed'],
            'columns' => [
                [
                    'attribute' => 'link',
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'display_mode',
                    'enableSorting' => false,
                    'value' => function (ImageLink $model) {
                        return $model->getDisplayModeObject()->getName();
                    },
                ],
                [
                    'attribute' => 'display_weight',
                    'enableSorting' => false,
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, ImageLink $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-picture"></span>', '#', [
                                'class' => 'view-image-link',
                                'title' => Yii::t('app', 'LABEL_VIEW'),
                                'data-toggle' => 'modal',
                                'data-target' => '#view-image-link-modal',
                                'data-key' => $model->key,
                            ]);
                        },
                        'update' => function ($url, ImageLink $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'update-image-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#update-image-link-modal',
                                'data-key' => $model->key,
                            ]);
                        },
                        'delete' => function ($url, ImageLink $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-erase"></span>',
                                ['image/delete-link', 'imageLinkKey' => $model->key],
                                [
                                    'title' => Yii::t('app', 'LABEL_DELETE'),
                                    'data-confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                                    'data-method' => 'post',
                                ]);
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>

    <?php Modal::begin([
        'id' => 'create-image-link-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'IMAGE_LINK_TITLE_CREATE') . '</h2>',
        'clientOptions' => ['backdrop' => 'static'],
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>

    <?php Modal::begin([
        'id' => 'update-image-link-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'IMAGE_LINK_TITLE_UPDATE') . '</h2>',
        'clientOptions' => ['backdrop' => 'static'],
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>

    <?php Modal::begin([
        'id' => 'view-image-link-modal',
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>
</div>
