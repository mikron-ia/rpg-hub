<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $externalDataDataProvider yii\data\ActiveDataProvider */

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
                    'value' => Html::a($model->epic->name, ['epic/view', 'key' => $model->key], []),
                ],
                [
                    'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                    'format' => 'shortSize',
                    'value' => strlen($model->data),
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
                [
                    'attribute' => 'importance_category',
                    'label' => Yii::t('app', 'GROUP_IMPORTANCE'),
                    'value' => $model->getImportanceCategory(),
                ],
                [
                    'label' => Yii::t('app', 'DESCRIPTION_COUNT_UNIQUE'),
                    'value' => $model->descriptionPack->getUniqueDescriptionTypesCount(),
                ],
                [
                    'label' => Yii::t('app', 'DESCRIPTION_COUNT_EXPECTED'),
                    'format' => 'raw',
                    'value' => $model->getImportanceCategoryObject()->minimum() . ' &mdash; ' . $model->getImportanceCategoryObject()->maximum(),
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
                [
                    'attribute' => 'master_group_id',
                    'format' => 'raw',
                    'value' => $model->masterGroup ?? '<em>' . Yii::t('app', 'GROUP_WITHOUT_MASTER') . '</em>',
                ],
                [
                    'label' => Yii::t('app', 'GROUP_SUBGROUPS'),
                    'format' => 'raw',
                    'value' => !empty($model->subGroups) ?
                        implode(',', $model->subGroups) :
                        '<em>' . Yii::t('app', 'GROUP_WITHOUT_SUBGROUPS') . '</em>',
                ],
                'display_as_tab:boolean',
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
            <?= Html::a(
                Yii::t('app', 'BUTTON_COPY_KEY'),
                '#',
                ['class' => 'btn btn-default', 'id' => 'button-copy-key']
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'group/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>

</div>
