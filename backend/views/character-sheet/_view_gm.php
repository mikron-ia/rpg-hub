<?php

use common\models\CharacterSheet;
use common\models\core\SeenStatus;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model CharacterSheet */
?>

<div class="col-md-12">

    <div class="col-md-6">

        <h2 class="text-center"><?= Yii::t('app', 'CHARACTER_SHEET_TECHNICAL') ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'key',
                ],
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key], []),
                ],
                [
                    'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->data),
                ],
                [
                    'label' => Yii::t('app', 'CHARACTER_SHEET_DATA_STATE'),
                    'format' => 'raw',
                    'value' => $model->getDataState()->getName(),
                ],
                [
                    'attribute' => 'currently_delivered_character_id',
                    'format' => 'raw',
                    'value' => isset($model->currently_delivered_character_id) ?
                        Html::a(
                            $model->currentlyDeliveredCharacter->name,
                            ['character/view', 'key' => $model->currentlyDeliveredCharacter->key]
                        ) :
                        null,
                ],
                [
                    'attribute' => 'player_id',
                    'value' => isset($model->player_id) ? $model->player->username : null
                ],
            ],
        ]) ?>

    </div>

    <?php if (!empty($model->notes)): ?>
        <div class="col-md-6">
            <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_NOTES') ?></h2>
            <?= $model->getNotesFormatted() ?>
        </div>
    <?php endif; ?>

    <div class="col-md-6">

        <h2 class="text-center"><?= Yii::t('app', 'SEEN_READ') ?></h2>
        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_SEEN),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'seen_at',
                    'format' => 'datetime',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'times_seen',
                    'format' => 'integer',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'times_seen_since_update',
                    'format' => 'integer',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
            ],
        ]) ?>

        <h2 class="text-center"><?= Yii::t('app', 'SEEN_BEFORE_UPDATE') ?></h2>
        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_UPDATED),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'seen_at',
                    'format' => 'datetime',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'times_seen',
                    'format' => 'integer',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'times_seen_since_update',
                    'format' => 'integer',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
            ],
        ]) ?>

        <h2 class="text-center"><?= Yii::t('app', 'SEEN_NEW') ?></h2>
        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_NEW),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'noted_at',
                    'format' => 'datetime',
                    'enableSorting' => false,
                ],
            ],
        ]) ?>

    </div>

</div>
