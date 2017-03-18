<?php

use common\models\core\Language;
use common\models\GroupMembership;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $showPrivates bool */

?>

<div class="buttoned-header">
    <?= Html::a(
        '<span class="btn btn-success">' . Yii::t('app', 'MEMBERSHIP_BUTTON_ADD') . '</span>',
        '#',
        [
            'class' => 'add-membership-link',
            'title' => Yii::t('app', 'MEMBERSHIP_BUTTON_ADD'),
            'data-toggle' => 'modal',
            'data-target' => '#add-membership-modal',
        ]
    ); ?>
</div>


<div id="memberships">
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getGroupCharacterMembershipsOrderedByPosition(),
        ]),
        'summary' => '',
        'filterPosition' => null,
        'rowOptions' => function (GroupMembership $model, $key, $index, $grid) {
            return [
                'data-toggle' => 'tooltip',
                'title' => $model->short_text,
            ];
        },
        'columns' => [
            'character.name',
            [
                'attribute' => 'visibility',
                'value' => function (GroupMembership $model) {
                    return $model->getVisibility();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {detach} {up} {down}',
                'buttons' => [
                    'update' => function ($url, GroupMembership $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                            'class' => 'update-parameter-link',
                            'title' => Yii::t('app', 'LABEL_UPDATE'),
                            'data-toggle' => 'modal',
                            'data-target' => '#update-parameter-modal',
                            'data-id' => $key,
                        ]);
                    },
                    'detach' => function ($url, GroupMembership $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-remove"></span>',
                            ['group-membership/detach', 'id' => $model->group_membership_id],
                            [
                                'title' => Yii::t('app', 'LABEL_DETACH'),
                                'data-confirm' => Yii::t(
                                    'app',
                                    'GROUP_MEMBERSHIP_CONFIRMATION_DETACH {name}',
                                    ['name' => $model->character->name]
                                ),
                                'data-method' => 'post',
                            ]);
                    },
                    'up' => function ($url, GroupMembership $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-up"></span>',
                            ['group-membership/move-up', 'id' => $model->group_membership_id],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_UP'),
                            ]
                        );
                    },
                    'down' => function ($url, GroupMembership $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-down"></span>',
                            ['group-membership/move-down', 'id' => $model->group_membership_id],
                            [
                                'title' => Yii::t('app', 'LABEL_MOVE_DOWN'),
                            ]
                        );
                    },
                ]
            ],
        ],
    ]) ?>
</div>

<?php Modal::begin([
    'id' => 'add-membership-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'MEMBERSHIP_TITLE_ADD') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php $this->registerJs(
    "$('.add-membership-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['group-membership/create']) . "',
        {
            group_id: " . $model->group_id . "
        },
        function (data) {
            $('.modal-body').html(data);
            $('#add-membership-modal').modal();
        }
    );
});"
); ?>

<?php Modal::begin([
    'id' => 'modify-membership-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'MEMBERSHIP_TITLE_MODIFY') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php $this->registerJs(
    "$('.modify-membership-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['group-membership/update']) . "',
        {
            id: $(this).data('id')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#modify-membership-modal').modal();
        }
    );
});"
); ?>

<?php Modal::begin([
    'id' => 'membership-history-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'MEMBERSHIP_TITLE_HISTORY') . '</h2>',
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php $this->registerJs(
    "$('.membership-history-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['group-membership/history']) . "',
        {
            id: $(this).data('id')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#membership-history-modal').modal();
        }
    );
});"
); ?>
