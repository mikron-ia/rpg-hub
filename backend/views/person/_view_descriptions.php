<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

/* @var $model common\models\Description */
?>

<div class="col-md-6">

    <div class="buttoned-header">
        <h3>
            <?= $model->getTypeName(); ?>
            (<?= mb_strtolower($model->getLanguage()); ?>, <?= mb_strtolower($model->getVisibility()); ?>)
        </h3>

        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            '#', [
                'class' => 'btn btn-primary update-description-link',
                'title' => Yii::t('app', 'LABEL_UPDATE'),
                'data-toggle' => 'modal',
                'data-target' => '#update-description-modal',
                'data-id' => $model->description_id,
            ]
        ); ?>
        <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['description/delete', 'id' => $model->description_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <div>
        <?= $model->getPublicFormatted(); ?>
    </div>

    <?php if ($model->private_text): ?>
        <h4><?= Yii::t('app', 'DESCRIPTION_TITLE_PRIVATE'); ?></h4>

        <div>
            <?= $model->getPrivateFormatted(); ?>
        </div>
    <?php endif; ?>

</div>
