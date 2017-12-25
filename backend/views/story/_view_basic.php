<?php

use common\models\Parameter;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

?>

<div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_BASIC_DATA_AND_OPERATIONS'); ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'key',
                ],
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key], []),
                ],
                [
                    'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->data),
                ],
                [
                    'attribute' => 'position',
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibility(),
                ],
                [
                    'label' => Yii::t('app', 'STORY_SHORT_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->short),
                ],
                [
                    'label' => Yii::t('app', 'STORY_LONG_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->long),
                ],
                [
                    'label' => Yii::t('app', 'STORY_WORD_COUNT'),
                    'value' => $model->getLongDescriptionWordCount(),
                ],
            ],
        ]) ?>

        <div class="text-center">
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            );
            ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_MOVE_DOWN'),
                ['story/move-up', 'key' => $model->key],
                [
                    'class' => 'btn btn-default',
                    'data' => [
                        'method' => 'post',
                    ],
                ]
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_MOVE_UP'),
                ['story/move-down', 'key' => $model->key],
                [
                    'class' => 'btn btn-default',
                    'data' => [
                        'method' => 'post',
                    ],
                ]
            ); ?>
            <?= \yii\helpers\Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'story/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>

    <div class="story-view col-md-6">
        <h2 class="text-center"><?php echo $model->getAttributeLabel('short'); ?></h2>
        <div>
            <?php echo $model->getShortFormatted(); ?>
        </div>
    </div>

    <div class="col-md-6">

        <div class="buttoned-header">
            <h2 class="text-center"><?= Yii::t('app', 'PARAMETER_TITLE_INDEX') ?></h2>
            <?= Html::a(
                '<span class="btn btn-success">' . Yii::t('app', 'BUTTON_PARAMETER_CREATE') . '</span>',
                '#',
                [
                    'class' => 'create-parameter-link',
                    'title' => Yii::t('app', 'BUTTON_PARAMETER_CREATE'),
                    'data-toggle' => 'modal',
                    'data-target' => '#create-parameter-modal'
                ]
            ); ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->parameterPack->getParametersOrdered()]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'code',
                    'enableSorting' => false,
                    'value' => function (Parameter $model) {
                        return $model->getCodeName();
                    },
                ],
                [
                    'attribute' => 'visibility',
                    'enableSorting' => false,
                    'value' => function (Parameter $model) {
                        return $model->getVisibilityName();
                    },
                ],
                [
                    'attribute' => 'content',
                    'enableSorting' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete} {up} {down}',
                    'contentOptions' => ['class' => 'text-center'],
                    'buttons' => [
                        'update' => function ($url, Parameter $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'update-parameter-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#update-parameter-modal',
                                'data-id' => $key,
                            ]);
                        },
                        'delete' => function ($url, Parameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-erase"></span>',
                                ['parameter/delete', 'id' => $model->parameter_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_DELETE'),
                                    'data-confirm' => Yii::t(
                                        'app',
                                        'CONFIRMATION_DELETE {name}',
                                        ['name' => $model->getCodeName()]
                                    ),
                                    'data-method' => 'post',
                                ]);
                        },
                        'up' => function ($url, Parameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-arrow-up"></span>',
                                ['parameter/move-up', 'id' => $model->parameter_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_MOVE_UP'),
                                ]
                            );
                        },
                        'down' => function ($url, Parameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-arrow-down"></span>',
                                ['parameter/move-down', 'id' => $model->parameter_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_MOVE_DOWN'),
                                ]
                            );
                        },
                    ]
                ],
            ],
        ]); ?>

    </div>

    <?php Modal::begin([
        'id' => 'create-parameter-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_CREATE') . '</h2>',
        'clientOptions' => ['backdrop' => 'static'],
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>

    <?php $this->registerJs(
        "$('.create-parameter-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['parameter/create']) . "',
        {
            pack_id: " . $model->parameterPack->parameter_pack_id . "
        },
        function (data) {
            $('.modal-body').html(data);
            $('#create-parameter-modal').modal();
        }
    );
});"
    ); ?>

    <?php Modal::begin([
        'id' => 'update-parameter-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_UPDATE') . '</h2>',
        'clientOptions' => ['backdrop' => 'static'],
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>

    <?php $this->registerJs(
        "$('.update-parameter-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['parameter/update']) . "',
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
