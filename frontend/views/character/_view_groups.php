<?php

use common\models\Character;
use common\models\core\Visibility;
use common\models\GroupMembership;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $header string */
/* @var $model Character */

?>

<?php if ($model->groupMembershipsVisibleToUser): ?>
    <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider(['allModels' => $model->groupMembershipsVisibleToUser]),
        'summary' => '',
        'rowOptions' => function (GroupMembership $model, $key, $index, $grid) {
            $options = [];
            if ($model->visibility === Visibility::VISIBILITY_GM) {
                $options['class'] = 'table-row-hidden secret';
            }
            return $options;
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
                'attribute' => 'short_text',
                'label' => Yii::t('app', 'GROUP_MEMBERSHIP_SHORT_TEXT'),
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

