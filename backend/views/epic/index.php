<?php

use common\models\Epic;
use common\models\EpicQuery;
use common\models\ParticipantRole;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $searchModel EpicQuery */
/* @var $dataProvider ActiveDataProvider */
/* @var $showManagerButton bool */

$this->title = Yii::t('app', 'TITLE_EPICS');
$this->params['breadcrumbs'][] = $this->title;

$epicRolesCalculatorClosure = function (Epic $model) {
    $participant = array_values(array_filter($model->participants, function ($participant) {
        return $participant->user_id === Yii::$app->user->id;
    }))[0] ?? null; // todo Switch to array_find() once PHP 8.4 is allowed

    return implode(', ', array_map(function (ParticipantRole $role) {
        return $role->getRoleDescribed();
    }, $participant?->participantRoles ?? []));
}; // it is detached to avoid big code block inside the widget config
?>
<div class="epic-index">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_EPIC_ADD_AS_GM'),
            ['create-as-gm'],
            ['class' => 'btn btn-success']
        ) ?>
        <?php if ($showManagerButton): ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_EPIC_ADD_AS_MANAGER'),
                ['create-as-manager'],
                ['class' => 'btn btn-success']
            ) ?>
        <?php endif; ?>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'system',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'status',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'format' => 'raw',
                    'value' => function (Epic $model) {
                        return '<span class="epic-status epic-status-in-cell ' . $model->getStatusClass() . '">' . $model->getStatus() . '</span>';
                    }
                ],
                [
                    'label' => Yii::t('app', 'EPIC_COUNT_PARTICIPANTS'),
                    'value' => function (Epic $model) {
                        return sprintf('%d', $model->getParticipants()->count());
                    },
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'label' => Yii::t('app', 'EPIC_PARTICIPANT_ROLES'),
                    'value' => $epicRolesCalculatorClosure,
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['class' => 'action-cell'],
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, Epic $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Yii::$app->urlManager->createUrl(['epic/view', 'key' => $model->key]),
                                ['title' => Yii::t('app', 'BUTTON_VIEW')]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>
