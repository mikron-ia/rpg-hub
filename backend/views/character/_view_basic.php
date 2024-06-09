<?php

use common\models\ExternalData;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Character */
/* @var $externalDataDataProvider yii\data\ActiveDataProvider */

?>

<div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_BASIC_DATA_AND_OPERATIONS'); ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/view', 'key' => $model->key], []),
                ],
                [
                    'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->data),
                ],
                [
                    'attribute' => 'character_sheet_id',
                    'format' => 'raw',
                    'value' => $model->character_sheet_id ?
                        Html::a(
                            $model->character->name,
                            ['character-sheet/view', 'key' => $model->character->key],
                            []
                        ) :
                        null,
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
                [
                    'attribute' => 'importance_category',
                    'label' => Yii::t('app', 'CHARACTER_IMPORTANCE'),
                    'value' => $model->getImportanceCategory(),
                ],
                [
                    'label' => Yii::t('app', 'DESCRIPTION_COUNT_UNIQUE'),
                    'value' => $model->descriptionPack->getUniqueDescriptionTypesCount(),
                ],
                [
                    'label' => Yii::t('app', 'DESCRIPTION_COUNT_EXPECTED'),
                    'format' => 'raw',
                    'value' => $model->getImportanceCategoryObject()->minimum() . ' &mdash; ' . $model->getImportanceCategoryObject()->maximum(),
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'datetime',
                ],
                [
                    'attribute' => 'modified_at',
                    'format' => 'datetime',
                ],
                [
                    'label' => Yii::t('app', 'IMPORTANCE_RECALCULATION_FLAG'),
                    'format' => 'boolean',
                    'value' => $model->importancePack->isFlaggedForRecalculation(),
                ],
            ],
        ]) ?>

        <div class="buttons-on-view">
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'key' => $model->key], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'character/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_EXTERNAL_DATA'); ?></h2>
        <?= GridView::widget([
            'dataProvider' => $externalDataDataProvider,
            'layout' => '{items}',
            'columns' => [
                [
                    'attribute' => 'code',
                    'enableSorting' => false,
                    'label' => Yii::t('external', 'EXTERNAL_DATA_CODE'),
                ],
                [
                    'attribute' => 'visibility',
                    'enableSorting' => false,
                    'label' => Yii::t('app', 'LABEL_VISIBILITY'),
                    'value' => function (ExternalData $model) {
                        return $model->getVisibility();
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, ExternalData $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'update-external-data-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#update-external-data-modal',
                                'data-id' => $key,
                            ]);
                        },
                        'delete' => function ($url, ExternalData $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-erase"></span>',
                                ['external-data/delete', 'id' => $model->external_data_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_DELETE'),
                                    'data-confirm' => Yii::t(
                                        'app',
                                        'CONFIRMATION_DELETE {name}',
                                        ['name' => $model->code]
                                    ),
                                    'data-method' => 'post',
                                ]);
                        }
                    ]
                ],
            ],
        ]); ?>

        <?php Modal::begin([
            'id' => 'update-external-data-modal',
            'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_UPDATE') . '</h2>',
            'clientOptions' => ['backdrop' => 'static'],
            'size' => Modal::SIZE_LARGE,
        ]); ?>

        <?php Modal::end(); ?>

    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_AUXILIARY_ACTIONS'); ?></h2>
        <div class="buttons-on-view">
            <?= Html::a(
                Yii::t('app', 'BUTTON_CREATE_CHARACTER_SHEET'),
                ['create-sheet', 'key' => $model->key],
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_CREATE_CHARACTER_SHEET'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_LOAD'), ['load-data', 'key' => $model->key], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_LOAD'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_MARK_AS_CHANGED'),
                ['mark-changed', 'key' => $model->key],
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_MARK_AS_CHANGED'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <span class="hidden" id="key-value" data-key="CH:<?= $model->key ?>"></span>
            <span class="hidden" id="button-message-copy-base"><?= Yii::t('app', 'BUTTON_COPY_KEY') ?></span>
            <span class="hidden" id="button-message-copy-confirm"><?= Yii::t('app', 'BUTTON_COPY_IN_PROGRESS') ?></span>
            <?= Html::a(
                Yii::t('app', 'BUTTON_COPY_KEY'),
                '#',
                ['class' => 'btn btn-default', 'id' => 'button-copy-key']
            ) ?>
        </div>
    </div>

</div>
