<?php
/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $showPrivates bool */
?>

<div class="col-md-8">
    <?php if ($model->descriptionPack): ?>
        <div id="descriptions">

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query' => $model->descriptionPack->getDescriptionsInLanguageOfTheActiveUser(),
                    'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
                ]),
                'itemOptions' => ['class' => 'item'],
                'summary' => '',
                'itemView' => function (\common\models\Description $model, $key, $index, $widget) {
                    return $this->render(
                        '_view_description',
                        [
                            'model' => $model,
                            'key' => $key,
                            'index' => $index,
                            'widget' => $widget,
                            'showPrivates' => $this->params['showPrivates']
                        ]
                    );
                },
            ]) ?>

        </div>
    <?php else: ?>
        <p><?= Yii::t('app', 'DESCRIPTIONS_NOT_FOUND'); ?></p>
    <?php endif; ?>
</div>

<div class="col-md-4">
    <h3><?= Yii::t('app', 'GROUP_MEMBERSHIP_LIST'); ?></h3>
    <?php if ($model->groupCharacterMemberships): ?>
        <div id="memberships">

            <?= \yii\grid\GridView::widget([
                'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $model->groupCharacterMemberships]),
                'summary' => '',
                'columns' => [
                    'character.name',
                    'short_text',
                ],
            ]); ?>

        </div>
    <?php else: ?>
        <p><?= Yii::t('app', 'MEMBERSHIPS_NOT_FOUND'); ?></p>
    <?php endif; ?>
</div>
