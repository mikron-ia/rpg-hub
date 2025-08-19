<?php

use backend\assets\InvitationAsset;
use common\models\core\Language;
use common\models\UserInvitation;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;

InvitationAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'USER_INVITATION_INDEX_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INVITE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INDEX'), ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'email',
            [
                'attribute' => 'language',
                'value' => function (UserInvitation $model) {
                    return (Language::create($model->language))->getName();
                }
            ],
            [
                'attribute' => 'role',
                'label' => Yii::t('app', 'USER_INVITATION_ROLE'),
                'value' => function (UserInvitation $model) {
                    return $model->getIntendedRoleName();
                }
            ],
            'created_at:datetime',
            'opened_at:datetime',
            'used_at:datetime',
            'revoked_at:datetime',
            'valid_to:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{link} {revoke} {renew} {resend}',
                'contentOptions' => ['class' => 'text-center'],
                'buttons' => [
                    'link' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="open-modal-with-link glyphicon glyphicon-link" data-link="'
                            . $model->getInvitationLink()
                            . '"></span>',
                            '#',
                            [
                                'title' => Yii::t('app', 'USER_INVITATION_SHOW_LINK'),
                                'data-link' => $model->getInvitationLink(),
                            ]
                        );
                    },
                    'revoke' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-remove-circle"></span>',
                            ['user/revoke', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'USER_INVITATION_REVOKE'),
                                'data-confirm' => Yii::t('app', 'USER_INVITATION_REVOKE_CONFIRM'),
                                'data-method' => 'post',
                            ]
                        );
                    },
                    'resend' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-share"></span>',
                            ['user/resend', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'USER_INVITATION_RESEND'),
                                'data-confirm' => Yii::t('app', 'USER_INVITATION_RESENDING_CONFIRM'),
                                'data-method' => 'post',
                            ]
                        );
                    },
                    'renew' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-repeat"></span>',
                            ['user/renew', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'USER_INVITATION_RENEW'),
                                'data-confirm' => Yii::t('app', 'USER_INVITATION_RENEWAL_CONFIRM'),
                                'data-method' => 'post',
                            ]
                        );
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Modal::begin([
        'id' => 'invitation-link-display-modal',
        'header' => '<h2 class="modal-title">' . Yii::t('app', 'INVITATION_LINK_MODAL_TITLE') . '</h2>',
        'size' => Modal::SIZE_LARGE,
    ]); ?>

    <div>
        <span class="hidden" id="button-message-copy-base"><?= Yii::t('app', 'BUTTON_COPY_INVITE') ?></span>
        <span class="hidden" id="button-message-copy-confirm"><?= Yii::t('app', 'BUTTON_COPY_IN_PROGRESS') ?></span>
        <span class="hidden" id="button-message-copy-failure"><?= Yii::t('app', 'BUTTON_COPY_FAILED') ?></span>
        <?= Html::textarea(
            name: 'link-content',
            options: [
                'id' => 'invitation-link-content',
                'class' => 'invitation-link-content',
                'readonly' => 'readonly',
                'rows' => 2,
            ],
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_COPY_INVITE'),
            '#',
            ['class' => 'btn btn-default btn-copy-in-modal', 'id' => 'button-copy-link']
        ) ?>
    </div>

    <?php Modal::end(); ?>

</div>
