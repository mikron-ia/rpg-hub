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

        <div class="text-center">
            <?= Html::a(Yii::t('app', 'BUTTON_LOAD'), ['load-data', 'key' => $model->key], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_LOAD'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_MARK_AS_CHANGED_M'),
                ['mark-changed', 'key' => $model->key],
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_MARK_AS_CHANGED'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <span class="hidden" id="key-value" data-key="GR:<?= $model->key ?>"></span>
            <span class="hidden" id="button-message-copy-base"><?= Yii::t('app', 'BUTTON_COPY_KEY') ?></span>
            <span class="hidden" id="button-message-copy-confirm"><?= Yii::t('app', 'BUTTON_COPY_IN_PROGRESS') ?></span>
            <span class="hidden" id="button-message-copy-failure"><?= Yii::t('app', 'BUTTON_COPY_FAILED') ?></span>
            <?= Html::a(
                Yii::t('app', 'BUTTON_COPY_KEY'),
                '#',
                ['class' => 'btn btn-default', 'id' => 'button-copy-key']
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'location/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>

</div>
