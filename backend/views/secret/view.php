<?php

use backend\assets\SecretAsset;
use common\models\Secret;
use common\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Secret */

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SECRET_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

SecretAsset::register($this);
?>
<div class="secret-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
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
    </div>

    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <div class="clearfix"></div>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key]),
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

        <div class="buttons-on-view">
            <span class="hidden" id="key-value" data-key="SECRET:<?= $model->key ?>"></span>
            <span class="hidden" id="button-message-copy-base"><?= Yii::t('app', 'BUTTON_COPY_KEY') ?></span>
            <span class="hidden" id="button-message-copy-confirm"><?= Yii::t('app', 'BUTTON_COPY_IN_PROGRESS') ?></span>
            <span class="hidden" id="button-message-copy-failure"><?= Yii::t('app', 'BUTTON_COPY_FAILED') ?></span>
            <?= Html::a(
                Yii::t('app', 'BUTTON_COPY_KEY'),
                '#',
                ['class' => 'btn btn-default', 'id' => 'button-copy-key', 'style' => 'display: none;']
            ) ?>
        </div>

        <div id="key-div" style="display: none">
            <h2 class="text-center"><?= Yii::t('app', 'SECRET_FIELD_KEY'); ?></h2>
            <p class="info-box"><?= Yii::t('app', 'LABEL_KEY_TITLE_EXPLANATION') ?></p>
            <p class="key"><?= $model->key ?></p>
        </div>
    </div>

    <div class="col-md-6">
        <?= $this->render('../bestowed/_view_bestowed_form', [
            'model' => $model,
            'attribute' => 'bestowedAccessIds',
            'class' => 'Secret',
            'formId' => 'form-bestow-access',
            'listKey' => $model->bestowedList->key,
            'usersForDropdown' => $model->epic->getPlayerListForDropDown(),
        ]) ?>
    </div>

    <div class="col-md-6">
        <h3 class="text-center"><?= Yii::t('app', 'SECRET_FIELD_CONTENT') ?></h3>
        <div class="text-separating-box"><?= $model->getContentFormatted() ?></div>
    </div>

    <div class="col-md-6">
        <h3 class="text-center"><?= Yii::t('app', 'SECRET_FIELD_NOTES') ?></h3>
        <?php if ($model->notes) : ?>
            <div class="text-separating-box text-separating-box-private"><?= $model->getNotesFormatted() ?></div>
        <?php else : ?>
            <div class="no-data-box"><?= Yii::t('app', 'SECRET_NOTES_EMPTY') ?></div>
        <?php endif; ?>
    </div>

</div>
