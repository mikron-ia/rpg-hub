<?php

use common\models\StoryParameter;
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
    </div>

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
        ],
    ]) ?>

    <div class="col-lg-12">

        <div class="buttoned-header">
            <h2><?php echo $model->getAttributeLabel('storyParameters'); ?></h2>
            <?= Html::a(
                '<span class="btn btn-success">' . Yii::t('app', 'BUTTON_STORY_PARAMETER_CREATE') . '</span>',
                '#',
                [
                    'class' => 'create-story-parameter-link',
                    'title' => Yii::t('app', 'BUTTON_STORY_PARAMETER_CREATE'),
                    'data-toggle' => 'modal',
                    'data-target' => '#create-story-parameter-modal'
                ]
            ); ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => StoryParameter::find()->with('story')->where(['story_id' => $model->story_id])]),
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'code',
                    'enableSorting' => false,
                    'value' => function (StoryParameter $model) {
                        return $model->getCodeName();
                    },
                ],
                [
                    'attribute' => 'visibility',
                    'enableSorting' => false,
                    'value' => function (StoryParameter $model) {
                        return $model->getVisibilityName();
                    },
                ],
                [
                    'attribute' => 'content',
                    'enableSorting' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{parameter-update} {parameter-delete}',
                    'buttons' => [
                        'parameter-update' => function ($url, StoryParameter $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', '#', [
                                'class' => 'update-story-parameter-link',
                                'title' => Yii::t('app', 'LABEL_UPDATE'),
                                'data-toggle' => 'modal',
                                'data-target' => '#update-story-parameter-modal',
                                'data-id' => $key,
                            ]);
                        },
                        'parameter-delete' => function ($url, StoryParameter $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-erase"></span>', $url, [
                                'title' => Yii::t('app', 'LABEL_DELETE'),
                                'data-confirm' => Yii::t(
                                    'app',
                                    'CONFIRMATION_DELETE {name}',
                                    ['name' => $model->getCodeName()]
                                ),
                                'data-method' => 'post',
                            ]);
                        }
                    ]
                ],
            ],
        ]); ?>

    </div>

    <div class="col-lg-12">
        <h2><?php echo $model->getAttributeLabel('short'); ?></h2>

        <div>
            <?php echo $model->getShortFormatted(); ?>
        </div>
    </div>

    <div class="col-lg-12">
        <h2><?php echo $model->getAttributeLabel('long'); ?></h2>

        <div>
            <?php echo $model->getLongFormatted(); ?>
        </div>
    </div>

</div>

<?php \yii\bootstrap\Modal::begin([
    'id' => 'create-story-parameter-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'STORY_PARAMETER_TITLE_CREATE') . '</h2>',
]); ?>

<?php \yii\bootstrap\Modal::end(); ?>

<?php $this->registerJs(
    "$('.create-story-parameter-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['story/parameter-create']) . "',
        {
            story_id: " . $model->story_id . "
        },
        function (data) {
            $('.modal-body').html(data);
            $('#create-story-parameter-modal').modal();
        }
    );
});"
); ?>

<?php \yii\bootstrap\Modal::begin([
    'id' => 'update-story-parameter-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'STORY_PARAMETER_TITLE_UPDATE') . '</h2>',
]); ?>

<?php \yii\bootstrap\Modal::end(); ?>

<?php $this->registerJs(
    "$('.update-story-parameter-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['story/parameter-update']) . "',
        {
            id: $(this).closest('tr').data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-story-parameter-modal').modal();
        }
    );
});"
);
?>

<div class="col-lg-12">

    <h2>JSON</h2>

    <p class="text-left">
        <?= Html::encode($model->data) ?>
    </p>

</div>