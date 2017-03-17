<?php

use common\models\core\Language;
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
            'query' => $model->getGroupCharacterMemberships(),
        ]),
        'summary' => '',
        'filterPosition' => null,
        'columns' => [
            'character.name',
            [
                'attribute' => 'visibility',
                'value' => function (\common\models\GroupMembership $model) {
                    return $model->getVisibility();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
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
        '" . Yii::$app->urlManager->createUrl(['group/membership-add']) . "',
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
        '" . Yii::$app->urlManager->createUrl(['group/membership-modify']) . "',
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
        '" . Yii::$app->urlManager->createUrl(['group/membership-history']) . "',
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
