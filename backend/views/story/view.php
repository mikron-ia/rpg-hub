<?php

use common\models\Parameter;
use common\models\Seen;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'STORY_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'id' => $model->story_id],
            ['class' => 'btn btn-primary']
        );
        ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_DOWN'),
            ['story/move-up', 'id' => $model->story_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_UP'),
            ['story/move-down', 'id' => $model->story_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>
        <?= \yii\helpers\Html::a(
            Yii::t('app', 'BUTTON_SEE_FRONTEND'),
            Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                'story/view',
                'id' => $model->story_id
            ]),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

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
                    'value' => Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []),
                ],
                [
                    'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->data),
                ],
                [
                    'attribute' => 'position',
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibility(),
                ],
            ],
        ]) ?>

        <div class="buttoned-header">
            <h2><?= Yii::t('app', 'PARAMETER_TITLE_INDEX') ?></h2>
            <?= Html::a(
                '<span class="btn btn-success">' . Yii::t('app', 'BUTTON_PARAMETER_CREATE') . '</span>',
                '#',
                [
                    'class' => 'create-parameter-link',
                    'title' => Yii::t('app', 'BUTTON_PARAMETER_CREATE'),
                    'data-toggle' => 'modal',
                    'data-target' => '#create-parameter-modal'
                ]
            ); ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->parameterPack->getParametersOrdered()]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'code',
                    'enableSorting' => false,
                    'value' => function (Parameter $model) {
                        return $model->getCodeName();
                    },
                ],
                [
                    'attribute' => 'visibility',
                    'enableSorting' => false,
                    'value' => function (Parameter $model) {
                        return $model->getVisibilityName();
                    },
                ],
                [
                    'attribute' => 'content',
                    'enableSorting' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete} {up} {down}',
                    'contentOptions' => ['class' => 'text-center'],
                    'buttons' => [
                        'update' => function ($url, Parameter $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'update-parameter-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#update-parameter-modal',
                                'data-id' => $key,
                            ]);
                        },
                        'delete' => function ($url, Parameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-erase"></span>',
                                ['parameter/delete', 'id' => $model->parameter_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_DELETE'),
                                    'data-confirm' => Yii::t(
                                        'app',
                                        'CONFIRMATION_DELETE {name}',
                                        ['name' => $model->getCodeName()]
                                    ),
                                    'data-method' => 'post',
                                ]);
                        },
                        'up' => function ($url, Parameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-arrow-up"></span>',
                                ['parameter/move-up', 'id' => $model->parameter_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_MOVE_UP'),
                                ]
                            );
                        },
                        'down' => function ($url, Parameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-arrow-down"></span>',
                                ['parameter/move-down', 'id' => $model->parameter_id],
                                [
                                    'title' => Yii::t('app', 'LABEL_MOVE_DOWN'),
                                ]
                            );
                        },
                    ]
                ],
            ],
        ]); ?>

    </div>

    <div class="col-md-6">

        <h2><?php echo $model->getAttributeLabel('short'); ?></h2>

        <div>
            <?php echo $model->getShortFormatted(); ?>
        </div>

    </div>

</div>

<?php Modal::begin([
    'id' => 'create-parameter-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_CREATE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php $this->registerJs(
    "$('.create-parameter-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['parameter/create']) . "',
        {
            pack_id: " . $model->parameterPack->parameter_pack_id . "
        },
        function (data) {
            $('.modal-body').html(data);
            $('#create-parameter-modal').modal();
        }
    );
});"
); ?>

<?php Modal::begin([
    'id' => 'update-parameter-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'PARAMETER_TITLE_UPDATE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php $this->registerJs(
    "$('.update-parameter-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['parameter/update']) . "',
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

<div class="col-md-12">

    <h2><?php echo $model->getAttributeLabel('long'); ?></h2>

    <div>
        <?php echo $model->getLongFormatted(); ?>
    </div>

</div>

<div class="col-md-12">

    <h2>JSON</h2>

    <p class="text-left">
        <?= Html::encode($model->data) ?>
    </p>

</div>

<div class="col-md-12">

    <div class="col-md-6">

        <h2 class="text-center"><?= Yii::t('app', 'SEEN_READ') ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new ActiveDataProvider([
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
            'dataProvider' => new ActiveDataProvider([
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
            'dataProvider' => new ActiveDataProvider([
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