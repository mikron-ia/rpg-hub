<?php

use common\models\GroupMembership;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $header string */
/* @var $model \common\models\Character */
?>

<div>
    <?php if ($model->groupMemberships): ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $model->groupMemberships]),
            'summary' => '',
            'rowOptions' => function (GroupMembership $model, $key, $index, $grid) {
                return [
                    'data-toggle' => 'tooltip',
                    'title' => $model->short_text,
                ];
            },
            'columns' => [
                [
                    'attribute' => 'group.name',
                    'label' => Yii::t('app', 'LABEL_GROUP'),
                    'format' => 'raw',
                    'value' => function (GroupMembership $model, $key, $index, $widget) {
                        return $model->group . ($model->group->master_group_id ? ' (' . $model->group->masterGroup . ')' : '');
                    },
                ],
                [
                    'attribute' => 'status',
                    'label' => Yii::t('app', 'GROUP_MEMBERSHIP_STATUS'),
                    'value' => function (GroupMembership $model, $key, $index, $widget) {
                        return $model->getStatus();
                    },
                ]
            ],
        ]); ?>
    <?php else: ?>
        <p class="info-box"><?= Yii::t('app', 'GROUPS_NOT_FOUND'); ?></p>
    <?php endif; ?>
</div>