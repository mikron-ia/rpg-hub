<?php
/* @var $this yii\web\View */
/* @var $header string */
/* @var $models \common\models\Group[] */
?>

<?php if ($models): ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $models]),
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'character.name',
                'label' => Yii::t('app', 'CHARACTER_NAME'),
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

