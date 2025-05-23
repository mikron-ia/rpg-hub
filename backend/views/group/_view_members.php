<?php

use common\models\GroupMembership;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\StringHelper;

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
            'data-group-id' => $model->group_id,
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
            [
                'attribute' => 'character.name',
                'format' => 'raw',
                'value' => function (GroupMembership $model) {
                    return Html::a(
                        StringHelper::truncateWords($model->character->name, 5, ' (...)', false),
                        ['character/view', 'key' => $model->character->key]
                    );
                }
            ],
            [
                'attribute' => 'visibility',
                'value' => function (GroupMembership $model) {
                    return $model->getVisibilityName();
                },
                'enableSorting' => false
            ],
            [
                'attribute' => 'status',
                'value' => function (GroupMembership $model) {
                    return $model->getStatus();
                },
                'enableSorting' => false
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {history} {update} {up} {down}',
                'buttons' => [
                    'view' => function ($url, GroupMembership $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '#', [
                            'class' => 'view-membership-link',
                            'title' => Yii::t('app', 'LABEL_VIEW'),
                            'data-toggle' => 'modal',
                            'data-target' => '#view-membership-modal',
                            'data-id' => $key,
                        ]);
                    },
                    'history' => function ($url, GroupMembership $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', '#', [
                            'class' => 'membership-history-link',
                            'title' => Yii::t('app', 'LABEL_HISTORY'),
                            'data-toggle' => 'modal',
                            'data-target' => '#membership-history-modal',
                            'data-id' => $key,
                        ]);
                    },
                    'update' => function ($url, GroupMembership $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                            'class' => 'update-membership-link',
                            'title' => Yii::t('app', 'LABEL_UPDATE'),
                            'data-toggle' => 'modal',
                            'data-target' => '#update-membership-modal',
                            'data-id' => $key,
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

<?php Modal::begin([
    'id' => 'view-membership-modal',
    'header' => '<h2 class="modal-title" id="membership-view-modal-title">' . Yii::t(
            'app',
            'MEMBERSHIP_TITLE_VIEW'
        ) . '</h2>',
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'update-membership-modal',
    'header' => '<h2 class="modal-title" id="membership-update-modal-title">' . Yii::t(
            'app',
            'MEMBERSHIP_TITLE_MODIFY'
        ) . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'membership-history-modal',
    'header' => '<h2 class="modal-title" id="membership-history-modal-title">' . Yii::t(
            'app',
            'MEMBERSHIP_TITLE_HISTORY'
        ) . '</h2>',
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

