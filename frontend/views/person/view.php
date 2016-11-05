<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Person */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_PEOPLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $model->canUserControlYou();

?>
<div class="person-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="subtitle"><?= $model->tagline; ?></p>

    <div class="col-md-12">
        <?php if ($model->canUserControlYou()) {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'visibility',
                        'value' => $model->getVisibilityName(),
                    ],
                ],
            ]);
        } ?>
    </div>

    <div class="clearfix"></div>

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
                        '_view_descriptions',
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

            <div class="clearfix"></div>

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
