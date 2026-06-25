<?php

/* @var $this yii\web\View */

/* @var $model common\models\Article */

use backend\assets\ArticleAsset;
use common\models\core\Visibility;
use yii\helpers\Html;
use yii\widgets\DetailView;

ArticleAsset::register($this);

?>
<div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_BASICS') ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => $model->epic_id
                        ? (Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key], []))
                        : Yii::t('app', 'ARTICLE_NO_EPIC'),
                ],
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName()
                ],
                [
                    'label' => Yii::t('app', 'ARTICLE_OUTLINE_CHARACTER_COUNT'),
                    'value' => strlen($model->outline_raw ?? ''),
                ],
                [
                    'label' => Yii::t('app', 'ARTICLE_TEXT_CHARACTER_COUNT'),
                    'value' => strlen($model->text_raw ?? ''),
                ],
                [
                    'label' => Yii::t('app', 'ARTICLE_OUTLINE_WORD_COUNT'),
                    'value' => $model->getOutlineWordCount(),
                ],
                [
                    'label' => Yii::t('app', 'ARTICLE_TEXT_WORD_COUNT'),
                    'value' => $model->getTextWordCount(),
                ],
            ],
        ]) ?>
        <div class="text-center buttons-on-view">
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_DELETE'),
                ['delete', 'key' => $model->key],
                [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'article/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_OUTLINE_TITLE') ?></h2>
        <?php if (!empty($model->outline_raw)): ?>
            <?= $model->getOutlinedFormatted() ?>
        <?php else: ?>
            <p class="error-box"><?= Yii::t('app', 'ARTICLE_OUTLINE_EMPTY') ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($model->notes_raw)): ?>
        <div class="col-md-6">
            <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_NOTES') ?></h2>
            <?= $model->getNotesFormatted() ?>
        </div>
    <?php endif; ?>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'BESTOWED_VISIBILITY_HEADER'); ?></h2>
        <?php if (!empty($model->bestowed_list_id) && $model->visibility === Visibility::Designated->value): ?>
            <?= $this->render('../bestowed/_view_bestowed_form', [
                'model' => $model,
                'attribute' => 'bestowedAccessIds',
                'class' => 'Article',
                'formId' => 'form-bestow-access',
                'listKey' => $model->bestowedList->key,
                'usersForDropdown' => $model->epic->getPlayerListForDropDown(),
            ]) ?>
        <?php else: ?>
            <p class="info-box">
                <?= Yii::t(
                    'app',
                    'BESTOWED_VISIBILITY_NOT_ACTIVE {currentVisibility} {targetVisibility}',
                    [
                        'currentVisibility' => $model->getVisibility()->getNameLowercase(),
                        'targetVisibility' => Visibility::Designated->getNameLowercase(),
                    ]
                ) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="col-md-6" id="key-div" style="display: none">
        <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_KEY'); ?></h2>
        <p class="info-box"><?= Yii::t('app', 'LABEL_KEY_TITLE_EXPLANATION') ?></p>
        <p class="key"><?= $model->key ?></p>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_AUXILIARY_ACTIONS'); ?></h2>
        <div class="buttons-on-view">
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
            <span class="hidden" id="key-value" data-key="ST:<?= $model->key ?>"></span>
            <span class="hidden" id="button-message-copy-base"><?= Yii::t('app', 'BUTTON_COPY_KEY') ?></span>
            <span class="hidden" id="button-message-copy-confirm"><?= Yii::t('app', 'BUTTON_COPY_IN_PROGRESS') ?></span>
            <span class="hidden" id="button-message-copy-failure"><?= Yii::t('app', 'BUTTON_COPY_FAILED') ?></span>
            <?= Html::a(
                Yii::t('app', 'BUTTON_COPY_KEY'),
                '#',
                ['class' => 'btn btn-default', 'id' => 'button-copy-key', 'style' => 'display: none;']
            ) ?>
        </div>
    </div>

</div>