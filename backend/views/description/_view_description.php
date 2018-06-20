<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Description */

?>

<div class="col-md-6 description-box" data-description-id="<?= $model->description_id ?>">

    <div class="buttoned-header">

        <h2><?= $model->position; ?>. <?= $model->getTypeName(); ?></h2>

        <div>
            <span class="btn btn-default move-down" data-description-id="<?= $model->description_id ?>"><?= Yii::t('app', 'BUTTON_MOVE_DOWN') ?></span>
            <span class="btn btn-default move-up" data-description-id="<?= $model->description_id ?>"><?= Yii::t('app', 'BUTTON_MOVE_UP') ?></span>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                '#',
                [
                    'class' => 'btn btn-primary update-description-link',
                    'title' => Yii::t('app', 'LABEL_UPDATE'),
                    'data-toggle' => 'modal',
                    'data-target' => '#update-description-modal',
                    'data-id' => $model->description_id,
                ]
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_DELETE'),
                ['description/delete', 'id' => $model->description_id],
                [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                        'method' => 'post',
                    ],
                ]
            ); ?>

            <?= Html::a(
                Yii::t('app', 'BUTTON_SHOW_HISTORY'),
                '#',
                [
                    'class' => 'btn btn-default description-history-link',
                    'data-toggle' => 'modal',
                    'data-target' => '#description-history-modal',
                    'data-id' => $model->description_id,
                ]
            ); ?>
        </div>

    </div>

    <div>

        <span class="tag-box">
            <?= Yii::t('app', 'DESCRIPTION_UPDATED {when} {who}', [
                'when' => Yii::$app->formatter->asDatetime($model->updated_at),
                'who' => $model->updatedBy->username,
            ]); ?>
        </span>

        <span class="tag-box">
            <?= Yii::t('app', 'DESCRIPTION_UPDATED_IC {when}', [
                'when' => $model->pointInTime->name ?? '?',
            ]); ?>
        </span>

        <span class="tag-box">
            <?= Yii::t('app', 'LABEL_VISIBLE') . ' ' . $model->getVisibilityLowercase(); ?>
        </span>

        <span class="tag-box">
            <?= $model->getLanguage(); ?>
        </span>

    </div>

    <div class="col-md-12 public-notes">
        <?= $model->getPublicFormatted(); ?>
    </div>

    <div class="col-md-12 protected-notes">
        <?= $model->getProtectedFormatted(); ?>
    </div>

    <div class="col-md-12 private-notes">
        <?= $model->getPrivateFormatted(); ?>
    </div>

</div>
