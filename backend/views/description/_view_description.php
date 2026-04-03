<?php

use common\models\Description;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\View;

/* @var $this View */
/* @var $model Description */

$messageForStillValid = isset($model->point_in_time_still_valid_id) && isset($model->point_in_time_end_id)
    ? Yii::t('app', 'DESCRIPTION_STILL_VALID_IC_CMS_HIDDEN {when}', ['when' => $model->pointInTimeStillValid->name ?? '?'])
    : Yii::t('app', 'DESCRIPTION_STILL_VALID_IC_CMS {when}', ['when' => $model->pointInTimeStillValid->name ?? '?']);

?>

<div class="col-md-6 description-box" data-description-key="<?= $model->key ?>">

    <div class="buttoned-header">
        <h2><?= $model->position; ?>. <?= htmlspecialchars($model->getTypeName()); ?></h2>
    </div>

    <div class="buttons-under-header">
        <span class="btn btn-default move-down" data-description-key="<?= $model->key ?>"">
            <?= Yii::t('app', 'BUTTON_MOVE_DOWN') ?>
        </span>

        <span class="btn btn-default move-up"  data-description-key="<?= $model->key ?>"">
            <?= Yii::t('app', 'BUTTON_MOVE_UP') ?>
        </span>

        <?= Html::a(
            Yii::t('app', 'BUTTON_SET_AS_CURRENT'),
            ['description/set-as-current', 'key' => $model->key],
            [
                'class' => 'btn btn-default',
                'title' => Yii::t('app', 'BUTTON_SET_AS_CURRENT_TITLE'),
                'data' => [
                    'confirm' => Yii::t('app', 'BUTTON_SET_AS_CURRENT_CONFIRM_REQUEST'),
                    'method' => 'patch',
                ],
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
                'data-key' => $model->key,
            ]
        ); ?>

        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['description/delete', 'key' => $model->key],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'delete',
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
                'data-key' => $model->key,
            ]
        ); ?>
    </div>

    <div>

        <span class="tag-box">
            <?= Yii::t('app', 'DESCRIPTION_UPDATED {when} {who}', [
                'when' => Yii::$app->formatter->asDatetime($model->updated_at),
                'who' => $model->updatedBy->username,
            ]); ?>
        </span>

        <span class="tag-box">
            <?= Yii::t('app', 'LABEL_VISIBLE') . ' ' . $model->getVisibilityLowercase(); ?>
        </span>

        <span class="tag-box">
            <?= Yii::t('app', 'DESCRIPTION_SINCE_IC {when}', [
                'when' => $model->pointInTimeStart->name ?? '?',
            ]); ?>
        </span>

        <span class="tag-box">
            <?= $messageForStillValid ?>
        </span>

        <span class="tag-box">
            <?= Yii::t('app', 'DESCRIPTION_EXPIRED_IC {when}', [
                'when' => $model->pointInTimeEnd->name ?? '?',
            ]); ?>
        </span>

        <?php if ($model->outdated): ?>
            <div class="tag-box">
                <?= Yii::t('app', 'DESCRIPTION_OUTDATED_TAG_CMS'); ?>
            </div>
        <?php endif; ?>

        <span class="tag-box">
            <?= $model->getLanguage(); ?>
        </span>

        <span class="tag-box">
            <?= Yii::t('app', 'DESCRIPTION_WORDS') ?>:
            <?= StringHelper::countWords($model->public_text); ?> /
            <?= StringHelper::countWords($model->protected_text); ?> /
            <?= StringHelper::countWords($model->private_text); ?>
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
