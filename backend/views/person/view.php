<?php

use common\models\core\Language;
use common\models\ExternalData;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
/* @var $externalDataDataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_PEOPLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="subtitle"><?= $model->tagline; ?></p>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_BASIC_DATA_AND_OPERATIONS'); ?></h2>
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
                    'attribute' => 'character_sheet_id',
                    'format' => 'raw',
                    'value' => $model->character_sheet_id ?
                        Html::a($model->character->name, ['character-sheet/view', 'id' => $model->character_sheet_id], []) :
                        null,
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
            ],
        ]) ?>

        <div class="text-center">
            <?= Html::a(Yii::t('app', 'BUTTON_LOAD'), ['load-data', 'id' => $model->character_id], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_LOAD'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->character_id],
                ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->character_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_EXTERNAL_DATA'); ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $externalDataDataProvider,
            'layout' => '{items}',
            'columns' => [
                [
                    'attribute' => 'code',
                    'enableSorting' => false,
                    'label' => Yii::t('external', 'EXTERNAL_DATA_CODE'),
                ],
                [
                    'attribute' => 'visibility',
                    'enableSorting' => false,
                    'label' => Yii::t('app', 'LABEL_VISIBILITY'),
                    'value' => function (ExternalData $model) {
                        return $model->getVisibility();
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, ExternalData $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'update-external-data-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#update-external-data-modal',
                                'data-id' => $key,
                            ]);
                        },
                        'delete' => function ($url, ExternalData $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-erase"></span>',
                                ['external-data/delete', 'id' => $model->external_data_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_DELETE'),
                                    'data-confirm' => Yii::t(
                                        'app',
                                        'CONFIRMATION_DELETE {name}',
                                        ['name' => $model->code]
                                    ),
                                    'data-method' => 'post',
                                ]);
                        }
                    ]
                ],
            ],
        ]); ?>

        <?php \yii\bootstrap\Modal::begin([
            'id' => 'update-external-data-modal',
            'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_UPDATE') . '</h2>',
        ]); ?>

        <?php \yii\bootstrap\Modal::end(); ?>

        <?php $this->registerJs(
            "$('.update-external-data-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['external-data/update']) . "',
        {
            id: $(this).closest('tr').data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-parameter-modal').modal();
        }
    );
});"
        );
        ?>
    </div>

    <div class="clearfix"></div>

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
            <?php foreach (Language::getLanguagesAsObjects() as $language): ?>
                <h3><?= $language->getName(); ?></h3>
                <?= \yii\widgets\ListView::widget([
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query' => $model->descriptionPack->getDescriptionsInLanguage($language),
                    'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
                ]),
                'itemOptions' => ['class' => 'item'],
                'summary' => '',
                'itemView' => function (\common\models\Description $model, $key, $index, $widget) {
                    return $this->render('_view_descriptions', ['model' => $model]);
                },
            ]) ?>
                <div class="clearfix"></div>
            <?php endforeach; ?>
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
