<?php
/* @var $this yii\web\View */
/* @var $header string */
/* @var $model \common\models\Character */
?>

<?php if ($model->groupMembershipsVisibleToUser): ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $model->groupMembershipsVisibleToUser]),
        'summary' => '',
        'rowOptions' => function (\common\models\GroupMembership $model, $key, $index, $grid) {
            $options = [];
            if ($model->visibility === \common\models\core\Visibility::VISIBILITY_GM) {
                $options['class'] = 'table-row-hidden';
            }
            return $options;
        },
        'columns' => [
            [
                'attribute' => 'group.name',
                'label' => Yii::t('app', 'LABEL_GROUP'),
                'format' => 'raw',
                'value' => function (\common\models\GroupMembership $model, $key, $index, $widget) {
                    return \yii\helpers\Html::a($model->group->name, ['group/view', 'key' => $model->group->key]);
                },
            ],
            [
                'attribute' => 'short_text',
                'label' => Yii::t('app', 'GROUP_MEMBERSHIP_SHORT_TEXT'),
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('app', 'GROUP_MEMBERSHIP_STATUS'),
                'value' => function (\common\models\GroupMembership $model, $key, $index, $widget) {
                    return $model->getStatus();
                },
            ]
        ],
    ]); ?>
<?php else: ?>
    <p class="info-box"><?= Yii::t('app', 'GROUPS_NOT_FOUND'); ?></p>
<?php endif; ?>

