<?php

use common\models\core\Language;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Character */
/* @var $showPrivates bool */

?>

<?php if ($model->description_pack_id): ?>
    <div class="buttoned-header">
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
                    return $this->render(
                        '_view_description',
                        [
                            'model' => $model,
                            'key' => $key,
                            'index' => $index,
                            'widget' => $widget,
                        ]
                    );
                },
            ]) ?>
        </div>
    <?php else: ?>
        <p><?= Yii::t('app', 'DESCRIPTIONS_NOT_FOUND'); ?></p>
    <?php endif; ?>

    <?php Modal::begin([
        'id' => 'create-description-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_CREATE') . '</h2>',
        'clientOptions' => ['backdrop' => 'static'],
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>

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

    <?php Modal::begin([
        'id' => 'update-description-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_UPDATE') . '</h2>',
        'clientOptions' => ['backdrop' => 'static'],
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>

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

    <?php Modal::begin([
        'id' => 'description-history-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_HISTORY') . '</h2>',
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <?php Modal::end(); ?>

    <?php $this->registerJs(
        "$('.description-history-link').click(function() {
    $.get(
        '" . Yii::$app->urlManager->createUrl(['description/history']) . "',
        {
            id: $(this).data('id')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#description-history-modal').modal();
        }
    );
});"
    ); ?>

<?php endif; ?>