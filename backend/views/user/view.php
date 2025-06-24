<?php

use common\models\core\Language;
use common\models\core\UserStatus;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'USER_VIEW_TITLE {user_name}', ['user_name' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'USER_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$confirmDisable = Yii::t('app', 'USER_CONFIRM_DISABLE');
$confirmDelete = Yii::t('app', 'USER_CONFIRM_DELETE');

?>
<div class="user-view">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_BASIC_DATA_AND_OPERATIONS'); ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'email:email',
                'created_at:datetime',
                'updated_at:datetime',
                ['attribute' => 'language', 'value' => (Language::create($model->language))->getName()],
                [
                    'attribute' => 'role',
                    'label' => Yii::t('app', 'USER_ROLE_NAME'),
                    'value' => $model->getUserRoleName(),
                ],
                [
                    'attribute' => 'status',
                    'label' => Yii::t('app', 'USER_STATUS_LABEL'),
                    'value' => fn(User $model) => UserStatus::from($model->status)->getName()
                ],
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_ACTIONS'); ?></h2>
        <div class="buttons-on-view">
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'id' => $model->id], ['class' => 'btn btn-primary'],
            ) ?>

            <?= Html::a(
                Yii::t('app', 'BUTTON_DISABLE'),
                ['disable', 'id' => $model->id],
                [
                    'class' => 'btn btn-danger',
                    'title' => Yii::t('app', 'BUTTON_DISABLE_USER_TITLE'),
                    'data' => [
                        'confirm' => $confirmDisable,
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'title' => Yii::t('app', 'BUTTON_DELETE_USER_TITLE'),
                'data' => [
                    'confirm' => $confirmDelete,
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="col-md-12">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_EPICS'); ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => Yii::t('app', 'USER_EPICS_PLAYED'),
                    'format' => 'raw',
                    'value' => implode(', ', $model->getEpicsPlayed()->orderBy(['name' => SORT_ASC])->all()),
                ],
                [
                    'label' => Yii::t('app', 'USER_EPICS_MASTERED'),
                    'format' => 'raw',
                    'value' => implode(', ', $model->getEpicsGameMastered()->orderBy(['name' => SORT_ASC])->all()),
                ],
                [
                    'label' => Yii::t('app', 'USER_EPICS_MANAGED'),
                    'format' => 'raw',
                    'value' => implode(', ', $model->getEpicsManaged()->orderBy(['name' => SORT_ASC])->all()),
                ],
                [
                    'label' => Yii::t('app', 'USER_EPICS_ASSISTED_IN'),
                    'format' => 'raw',
                    'value' => implode(', ', $model->getEpicsAssisted()->orderBy(['name' => SORT_ASC])->all()),
                ],
                [
                    'label' => Yii::t('app', 'USER_EPICS_TOTAL'),
                    'format' => 'raw',
                    'value' => implode(', ', $model->getEpics()->orderBy(['name' => SORT_ASC])->all()),
                ],
            ],
        ]) ?>
    </div>

</div>
