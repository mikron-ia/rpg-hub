<?php

use common\models\core\Language;
use common\models\UserInvitation;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'USER_INVITATION_INDEX_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INVITE'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'template' => '{revoke}',
                'buttons' => [
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
                ]
            ],
        ],
    ]); ?>

</div>
