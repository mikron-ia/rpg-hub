<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Description */

?>

<div class="col-md-6">

    <div class="buttoned-header">
        <h3><?= $model->position; ?>. <?= $model->getTypeName(); ?></h3>

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
            Yii::t('app', 'BUTTON_MOVE_DOWN'),
            ['description/move-down', 'id' => $model->description_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
            ]
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MOVE_UP'),
            ['description/move-up', 'id' => $model->description_id],
            [
                'class' => 'btn btn-default',
                'data' => [
                    'method' => 'post',
                ],
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
    </div>

    <p>
        <span class="tag-box">
            <?= Yii::t('app', 'LABEL_VISIBLE') . ' ' . $model->getVisibilityLowercase(); ?>
        </span>
        <span class="tag-box">
            <?= $model->getLanguage(); ?>
        </span>
        <span class="tag-box">
            <?= Yii::t('app', 'DESCRIPTION_UPDATED {when} {who}', [
                'when' => Yii::$app->formatter->asDatetime($model->updated_at),
                'who' => $model->updatedBy->username,
            ]); ?>
        </span>
    </p>

    <div>
        <?= $model->getPublicFormatted(); ?>
    </div>

    <div class="private-notes">
        <?= $model->getPrivateFormatted(); ?>
    </div>

</div>
