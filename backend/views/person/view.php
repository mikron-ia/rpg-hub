<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Person */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_PEOPLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->person_id],
            ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->person_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <p class="note"><?= $model->tagline; ?></p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'epic_id',
                'format' => 'raw',
                'value' => Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []),
            ],
            [
                'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                'format' => 'shortSize',
                'value' => strlen($model->data),
            ],
            [
                'attribute' => 'character_id',
                'format' => 'raw',
                'value' => $model->character_id ?
                    Html::a($model->character->name, ['character/view', 'id' => $model->character_id], []) :
                    null,
            ],
            [
                'attribute' => 'visibility',
                'value' => $model->getVisibilityName(),
            ],
        ],
    ]) ?>

    <h2 class="text-center"><?= Yii::t('app', 'LABEL_DESCRIPTIONS'); ?></h2>

    <?php if ($model->descriptionPack): ?>
        <div id="descriptions">
            <?= \yii\widgets\ListView::widget(
                [
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query' => $model->descriptionPack->getDescriptions()
                    ]),
                    'itemOptions' => ['class' => 'item'],
                    'summary' => '',
                    'itemView' => function (\common\models\Description $model, $key, $index, $widget) {
                        return $this->render('_view_descriptions', ['model' => $model]);
                    },
                ]
            ) ?>
        </div>
    <?php endif; ?>

</div>
