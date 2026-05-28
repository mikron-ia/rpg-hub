<?php

use common\models\Location;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Location */
/* @var $externalDataDataProvider ActiveDataProvider */

?>

<div>

    <div class="col-md-6">

        <div class="clearfix">&nbsp;</div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key], []),
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
                [
                    'attribute' => 'importance_category',
                    'label' => Yii::t('app', 'LOCATION_IMPORTANCE'),
                    'value' => $model->getImportanceCategory(),
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'datetime',
                ],
                [
                    'attribute' => 'modified_at',
                    'format' => 'datetime',
                ],
                [
                    'label' => Yii::t('app', 'IMPORTANCE_RECALCULATION_FLAG'),
                    'format' => 'boolean',
                    'value' => $model->importancePack->isFlaggedForRecalculation(),
                ],
            ],
        ]) ?>

        <div class="text-center buttons-on-view">
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <div class="col-md-6" id="key-div" style="display: none">
        <h2 class="text-center"><?= Yii::t('app', 'LOCATION_KEY'); ?></h2>
        <p class="info-box"><?= Yii::t('app', 'LABEL_KEY_TITLE_EXPLANATION') ?></p>
        <p class="key"><?= $model->key ?></p>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_AUXILIARY_ACTIONS'); ?></h2>
        <div class="buttons-on-view">
            <?= Html::a(
                Yii::t('app', 'BUTTON_MARK_AS_CHANGED_N'),
                ['mark-changed', 'key' => $model->key],
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_MARK_AS_CHANGED'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <span class="hidden" id="key-value" data-key="LOC:<?= $model->key ?>"></span>
            <span class="hidden" id="button-message-copy-base"><?= Yii::t('app', 'BUTTON_COPY_KEY') ?></span>
            <span class="hidden" id="button-message-copy-confirm"><?= Yii::t('app', 'BUTTON_COPY_IN_PROGRESS') ?></span>
            <span class="hidden" id="button-message-copy-failure"><?= Yii::t('app', 'BUTTON_COPY_FAILED') ?></span>
            <?= Html::a(
                Yii::t('app', 'BUTTON_COPY_KEY'),
                '#',
                ['class' => 'btn btn-default', 'id' => 'button-copy-key', 'style' => 'display: none;']
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'location/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default', 'id' => 'button-copy-key']
            ) ?>
        </div>
    </div>

</div>
