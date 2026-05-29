<?php

use common\models\Description;
use common\models\DescriptionPack;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\ListView;

/* @var $this View */
/* @var $model DescriptionPack */
/* @var $showPrivates bool */
/* @var $creatorController string */
/* @var $creatorKey string */

?>

<div class="buttoned-header">
    <p class="btn btn-success create-description-link"
       data-controller="<?= $creatorController ?>"
       data-key="<?= $creatorKey ?>"
    ><?= Yii::t('app', 'DESCRIPTION_BUTTON_CREATE') ?></p>
    <p class="btn btn-default open-description-help">
        <?= Yii::t('app', 'DESCRIPTION_BUTTON_HELP') ?>
    </p>
</div>

<?php if (count($model->descriptions) > 0): ?>
    <div id="descriptions">
        <?= ListView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->getDescriptions(),
                'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
            ]),
            'itemOptions' => ['class' => 'item'],
            'summary' => '',
            'itemView' => function (Description $model, $key, $index, $widget) {
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
    <p class="info-box"><?= Yii::t('app', 'DESCRIPTIONS_NOT_FOUND'); ?></p>
<?php endif; ?>

<?php Modal::begin([
    'id' => 'create-description-modal',
    'bodyOptions' => ['class' => 'modal-body modal-body-to-fill'],
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_CREATE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'update-description-modal',
    'bodyOptions' => ['class' => 'modal-body modal-body-to-fill'],
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_UPDATE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'description-history-modal',
    'bodyOptions' => ['class' => 'modal-body modal-body-to-fill'],
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_HISTORY') . '</h2>',
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'description-help-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_HELP') . '</h2>',
    'size' => Modal::SIZE_LARGE,
]); ?>
<?= $this->render('_help', ['model' => $model]) ?>
<?php Modal::end(); ?>
