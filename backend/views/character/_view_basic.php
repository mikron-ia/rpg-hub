<?php

use common\models\ExternalData;
use yii\bootstrap\Modal;
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
                    'value' => Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []),
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
                        Html::a($model->character->name, ['character-sheet/view', 'id' => $model->character_sheet_id],
                            []) :
                        null,
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
            ],
        ]) ?>

        <div class="text-center">
            <?= Html::a(Yii::t('app', 'BUTTON_LOAD'), ['load-data', 'id' => $model->character_id], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_LOAD'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_CREATE_CHARACTER_SHEET'),
                ['create-sheet', 'id' => $model->character_id],
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_CREATE_CHARACTER_SHEET'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'id' => $model->character_id],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->character_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_EXTERNAL_DATA'); ?></h2>
        <?= \yii\grid\GridView::widget([
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

        <?php $this->registerJs(
            "$('.update-external-data-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['external-data/update']) . "',
        {
            id: $(this).closest('tr').data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-parameter-modal').modal();
        }
    );
});"
        );
        ?>
    </div>

</div>
