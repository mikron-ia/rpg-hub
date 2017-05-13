<?php

use common\models\GroupMembership;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $header string */
/* @var $models \common\models\Group[] */
?>

<?php if ($models): ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new ArrayDataProvider(['allModels' => $models]),
        'summary' => '',
        'rowOptions' => function (GroupMembership $model, $key, $index, $grid) {
            $options = [];
            if ($model->visibility === \common\models\core\Visibility::VISIBILITY_GM) {
                $options['class'] = 'table-row-hidden secret';
            }
            return $options;
        },
        'columns' => [
            [
                'attribute' => 'character.name',
                'label' => Yii::t('app', 'CHARACTER_NAME'),
                'format' => 'raw',
                'value' => function (GroupMembership $model, $key, $index, $widget) {
                    return Html::a($model->character->name, ['character/view', 'key' => $model->character->key]);
                },
            ],
            [
                'attribute' => 'short_text',
                'label' => Yii::t('app', 'GROUP_MEMBERSHIP_SHORT_TEXT'),
            ]
        ],
    ]); ?>
<?php else: ?>
    <p class="info-box"><?= Yii::t('app', 'MEMBERSHIPS_NOT_FOUND'); ?></p>
<?php endif; ?>

