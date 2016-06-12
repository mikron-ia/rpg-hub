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

    <p class="subtitle"><?= $model->tagline; ?></p>

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

    <?php if ($model->description_pack_id): ?>
        <div class="buttoned-header">
            <h2 class="text-center"><?= Yii::t('app', 'LABEL_DESCRIPTIONS'); ?></h2>

            <?= Html::a(
                '<span class="btn btn-success">' . Yii::t('app', 'DESCRIPTION_BUTTON_CREATE') . '</span>',
                '#',
                [
                    'class' => 'create-description-link',
                    'title' => Yii::t('app', 'DESCRIPTION_BUTTON_CREATE'),
                    'data-toggle' => 'modal',
                    'data-target' => '#create-description-modal',
                ]
            ); ?>
        </div>
    <?php endif; ?>

    <?php if ($model->descriptionPack): ?>
        <div id="descriptions">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query' => $model->descriptionPack->getDescriptions(),
                    'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
                ]),
                'itemOptions' => ['class' => 'item'],
                'summary' => '',
                'itemView' => function (\common\models\Description $model, $key, $index, $widget) {
                    return $this->render('_view_descriptions', ['model' => $model]);
                },
            ]) ?>
        </div>
    <?php else: ?>
        <p><?= Yii::t('app', 'DESCRIPTIONS_NOT_FOUND'); ?></p>
    <?php endif; ?>

    <?php \yii\bootstrap\Modal::begin([
        'id' => 'create-description-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_CREATE') . '</h2>',
    ]); ?>

    <?php \yii\bootstrap\Modal::end(); ?>

    <?php $this->registerJs(
        "$('.create-description-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['description/create']) . "',
        {
            pack_id: " . $model->description_pack_id . "
        },
        function (data) {
            $('.modal-body').html(data);
            $('#create-description-modal').modal();
        }
    );
});"
    ); ?>

    <?php \yii\bootstrap\Modal::begin([
        'id' => 'update-description-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_UPDATE') . '</h2>',
    ]); ?>

    <?php \yii\bootstrap\Modal::end(); ?>

    <?php $this->registerJs(
        "$('.update-description-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['description/update']) . "',
        {
            id: $(this).data('id')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-description-modal').modal();
        }
    );
});"
    ); ?>

</div>
