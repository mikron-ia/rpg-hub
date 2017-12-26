<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DescriptionPack */
/* @var $showPrivates bool */

?>

<div class="buttoned-header">
    <p class="btn btn-success create-description-link" data-pack-id="<?= $model->description_pack_id ?>"><?= Yii::t('app', 'DESCRIPTION_BUTTON_CREATE') ?></p>
</div>

<?php if ($model): ?>
    <div id="descriptions">
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getDescriptions(),
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

<?php Modal::begin([
    'id' => 'update-description-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_UPDATE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'description-history-modal',
    'header' => '<h2 class="modal-title">' . Yii::t('app', 'DESCRIPTION_TITLE_HISTORY') . '</h2>',
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>
