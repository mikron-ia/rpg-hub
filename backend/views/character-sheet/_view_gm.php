<?php

/* @var $this yii\web\View */

/* @var $model common\models\CharacterSheet */

use common\models\Seen;
use yii\helpers\Html;
use yii\widgets\DetailView;

?>

<div class="col-md-12">

    <div class="col-md-6">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'key',
                ],
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key], []),
                ],
                [
                    'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->data),
                ],
                [
                    'attribute' => 'currently_delivered_character_id',
                    'format' => 'raw',
                    'value' => isset($model->currently_delivered_character_id) ?
                        Html::a(
                            $model->currentlyDeliveredPerson->name,
                            ['character/view', 'key' => $model->currentlyDeliveredPerson->key]
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

    <div class="col-md-6">

        <h2 class="text-center"><?= Yii::t('app', 'SEEN_READ') ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(Seen::STATUS_SEEN),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'seen_at',
                    'format' => 'datetime',
                    'enableSorting' => false,
                ],
            ],
        ]) ?>

    </div>

    <div class="col-md-6">

        <h2 class="text-center"><?= Yii::t('app', 'SEEN_BEFORE_UPDATE') ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(Seen::STATUS_UPDATED),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'seen_at',
                    'format' => 'datetime',
                    'enableSorting' => false,
                ],
            ],
        ]) ?>

    </div>

    <div class="col-md-6">

        <h2 class="text-center"><?= Yii::t('app', 'SEEN_NEW') ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(Seen::STATUS_NEW),
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