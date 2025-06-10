<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'USER_VIEW_TITLE {user_name}', ['user_name' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'USER_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_DISABLE'), ['disable', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'title' => Yii::t('app', 'BUTTON_DISABLE_USER_TITLE'),
            'data' => [
                'confirm' => 'Are you sure you want to disable this user?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'title' => Yii::t('app', 'BUTTON_DELETE_USER_TITLE'),
            'data' => [
                'confirm' => 'Are you sure you want to delete this user?',
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <div class="col-md-6>">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'email:email',
                'created_at:datetime',
                'updated_at:datetime',
                [
                    'attribute' => 'language',
                    'value' => (\common\models\core\Language::create($model->language))->getName()
                ],
                [
                    'attribute' => 'role',
                    'label' => Yii::t('app', 'USER_ROLE_NAME'),
                    'value' => $model->getUserRoleName()
                ],
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
