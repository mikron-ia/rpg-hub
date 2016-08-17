<?php

use common\models\Group;
use common\models\Parameter;
use common\models\Story;
use common\models\Participant;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Epic */

$this->title = Yii::t('app', 'LABEL_EPIC') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'id' => $model->epic_id],
            ['class' => 'btn btn-primary']);
        ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'key',
            'system',
        ],
    ]) ?>

    <div class="col-lg-6">

        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'EPIC_CARD_PARTICIPANTS'); ?></h2>
            <?= Html::a(
                '<span class="btn btn-success">' . Yii::t('app', 'BUTTON_PARTICIPANT_ADD') . '</span>',
                '#',
                [
                    'class' => 'add-user-link',
                    'title' => Yii::t('app', 'BUTTON_PARTICIPANT_ADD'),
                    'data-toggle' => 'modal',
                    'data-target' => '#add-user-modal'
                ]
            ); ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getParticipants()]),
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
                        return $model->getRoleDescribed();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function ($url, Participant $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'edit-user-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#edit-user-modal',
                                'data-id' => $key,
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>

        <?php \yii\bootstrap\Modal::begin([
            'id' => 'add-user-modal',
            'header' => '<h2 class="modal-title">' . Yii::t('app', 'EPIC_PARTICIPANT_ADD') . '</h2>',
        ]); ?>

        <?php \yii\bootstrap\Modal::end(); ?>

        <?php $this->registerJs(
            "$('.add-user-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['epic/participant-add']) . "',
        {
            epic_id: " . $model->epic_id . "
        },
        function (data) {            
            $('.modal-body').html(data);
            $('#add-user-modal').modal();
        }
    );
});"
        ); ?>

        <?php \yii\bootstrap\Modal::begin([
            'id' => 'update-user-modal',
            'header' => '<h2 class="modal-title">' . Yii::t('app', 'EPIC_PARTICIPANT_EDIT') . '</h2>',
        ]); ?>

        <?php \yii\bootstrap\Modal::end(); ?>

        <?php $this->registerJs(
            "$('.edit-user-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['epic/participant-edit']) . "',
        {
            participant_id: $(this).closest('tr').data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-user-modal').modal();
        }
    );
    $('.modal-title').html('" . Yii::t('app', 'EPIC_PARTICIPANT_EDIT') . ": ' + $(this).closest('tr').children().first().text());
});"
        ); ?>

    </div>

    <div class="col-lg-6">

        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'EPIC_HEADER_GROUPS'); ?></h2>
            <?= Html::a(Yii::t('app', 'BUTTON_GROUP_CREATE'), ['group/create'],
                ['class' => 'btn btn-success pull-right']); ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getGroups()->orderBy('group_id DESC'),
                'sort' => false,
            ]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, Group $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['group/view', 'id' => $model->group_id]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Group $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['group/update', 'id' => $model->group_id]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>

    </div>

    <div class="col-lg-6">

        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'EPIC_HEADER_STORIES'); ?></h2>
            <?= Html::a(Yii::t('app', 'BUTTON_STORY_CREATE'), ['story/create'],
                ['class' => 'btn btn-success pull-right']); ?>
        </div>
        <?= GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getStories()->orderBy('story_id DESC'),
                'sort' => false,
            ]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, Story $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['story/view', 'id' => $model->story_id]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, Story $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['story/update', 'id' => $model->story_id]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-lg-6">
        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'EPIC_HEADER_RECAPS'); ?></h2>
            <?= Html::a(Yii::t('app', 'BUTTON_RECAP_CREATE'), ['recap/create'], ['class' => 'btn btn-success']); ?>
        </div>
        <?= GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getRecaps()->orderBy('time DESC'),
                'sort' => false,
            ]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'time',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['recap/view', 'id' => $model->recap_id]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->urlManager->createUrl(['recap/update', 'id' => $model->recap_id]),
                                ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

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
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => Parameter::find()->where(['parameter_pack_id' => $model->parameter_pack_id])]),
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
                    'template' => '{update} {delete}',
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
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>

    <?php \yii\bootstrap\Modal::begin([
        'id' => 'create-parameter-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_CREATE') . '</h2>',
    ]); ?>

    <?php \yii\bootstrap\Modal::end(); ?>

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

    <?php \yii\bootstrap\Modal::begin([
        'id' => 'update-parameter-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_UPDATE') . '</h2>',
    ]); ?>

    <?php \yii\bootstrap\Modal::end(); ?>

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
