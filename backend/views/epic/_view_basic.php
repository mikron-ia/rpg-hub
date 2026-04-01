<?php

use common\models\Epic;
use common\models\Parameter;
use common\models\Participant;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Epic */
/* @var $externalDataDataProvider ActiveDataProvider */

?>

<div>

    <div class="col-lg-6">
        <h2><?= Yii::t('app', 'EPIC_BASIC') ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'key',
                'system',
                ['attribute' => 'status', 'value' => $model->getStatus()],
                ['attribute' => 'current_story_id', 'format' => 'raw', 'value' => $model->currentStory],
                ['label' => Yii::t('app', 'EPIC_BASIC_COUNT_STORIES'), 'value' => count($model->stories)],
                ['label' => Yii::t('app', 'EPIC_BASIC_COUNT_CHARACTERS'), 'value' => count($model->people)],
                ['label' => Yii::t('app', 'EPIC_BASIC_COUNT_GROUPS'), 'value' => count($model->groups)],
                ['label' => Yii::t('app', 'EPIC_BASIC_COUNT_CHARACTER_SHEETS'), 'value' => count($model->characters)],
                ['label' => Yii::t('app', 'EPIC_BASIC_COUNT_RECAPS'), 'value' => count($model->recaps)],
                ['label' => Yii::t('app', 'EPIC_BASIC_COUNT_POINTS_IN_TIME'), 'value' => count($model->pointsInTime)],
                [
                    'label' => Yii::t('app', 'EPIC_BASIC_COUNT_SESSIONS'),
                    'value' => $model->getGameCountByStatus('closed') . ' / ' . count($model->games),
                ],
                ['label' => Yii::t('app', 'EPIC_BASIC_COUNT_ARTICLES'), 'value' => count($model->articles)],
                ['attribute' => 'style', 'value' => $model->getStyle()->getStyleName()],
            ],
        ]) ?>
    </div>

    <div class="col-lg-6">

        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'EPIC_CARD_PARTICIPANTS'); ?></h2>
            <?= Html::a(
                '<span class="btn btn-success">' . Yii::t('app', 'BUTTON_PARTICIPANT_ADD') . '</span>',
                ['participant-add', 'key' => $model->key],
                ['title' => Yii::t('app', 'BUTTON_PARTICIPANT_ADD')]
            ); ?>
        </div>

        <div>
            <?= Yii::t('app', 'PARTICIPANT_REMOVE_INFORMATION') ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->getParticipants()]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'user.username',
                    'label' => Yii::t('app', 'EPIC_CARD_USERNAME'),
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'role',
                    'label' => Yii::t('app', 'EPIC_CARD_ROLE'),
                    'enableSorting' => false,
                    'value' => function (Participant $model) {
                        $roles = $model->getRolesList();
                        return !empty($roles) ? implode(', ', $roles) : null;
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, Participant $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                ['participant-edit', 'key' => $model->key],
                                ['title' => Yii::t('app', 'LABEL_UPDATE')],
                            );
                        },
                        'delete' => function ($url, Participant $model, $key) {
                            return empty($model->participantRoles)
                                ? Html::a(
                                    '<span class="glyphicon glyphicon-erase"></span>',
                                    ['participant-delete', 'key' => $model->key],
                                    [
                                        'title' => Yii::t('app', 'LABEL_DELETE'),
                                        'data-confirm' => Yii::t(
                                            'app',
                                            'CONFIRMATION_PARTICIPANT_REMOVE {name}',
                                            ['name' => $model->user->username]
                                        ),
                                        'data-method' => 'delete',
                                    ])
                                : '';
                        },
                    ],
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
            packKey: '" . $model->parameterPack->key . "'
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
            id: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-parameter-modal').modal();
        }
    );
});"
    );
    ?>

    <div class="col-lg-12">

        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'PARAMETER_TITLE_INDEX') ?></h2>
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
            'dataProvider' => new ActiveDataProvider(['query' => Parameter::find()->where(['parameter_pack_id' => $model->parameter_pack_id])]),
            'summary' => '',
            'options' => ['style' => 'table-layout: fixed'],
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
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, Parameter $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'update-parameter-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#update-parameter-modal',
                                'data-key' => $model->key,
                            ]);
                        },
                        'delete' => function ($url, Parameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-erase"></span>',
                                ['parameter/delete', 'key' => $model->key],
                                [
                                    'title' => Yii::t('app', 'LABEL_DELETE'),
                                    'data-confirm' => Yii::t(
                                        'app',
                                        'CONFIRMATION_DELETE {name}',
                                        ['name' => $model->getCodeName()]
                                    ),
                                    'data-method' => 'post',
                                ]);
                        }
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
            packKey: '" . $model->parameterPack->key . "'
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
            key: $(this).data('key')
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
