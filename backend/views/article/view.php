<?php

use common\models\core\SeenStatus;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_MARK_AS_CHANGED'),
            ['mark-changed', 'key' => $model->key],
            [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_MARK_AS_CHANGED'),
                    'method' => 'post',
                ],
            ]
        ) ?>
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

    <p class="subtitle"><?= $model->subtitle ?></p>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_BASICS') ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => $model->epic_id
                        ? (Html::a($model->epic->name, ['epic/view', 'key' => $model->key], []))
                        : Yii::t('app', 'ARTICLE_NO_EPIC'),
                ],
                'key',
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibility()
                ],
                [
                    'label' => Yii::t('app', 'ARTICLE_CHARACTER_COUNT'),
                    'value' => '?'
                ],
                [
                    'label' => Yii::t('app', 'ARTICLE_WORD_COUNT'),
                    'value' => '?'
                ],
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'SEEN_READ') ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_SEEN),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'seen_at',
                    'format' => 'datetime',
                    'enableSorting' => false,
                ],
            ],
        ]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'SEEN_BEFORE_UPDATE') ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_UPDATED),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'seen_at',
                    'format' => 'datetime',
                    'enableSorting' => false,
                ],
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'SEEN_NEW') ?></h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->seenPack->getSightingsWithStatus(SeenStatus::STATUS_NEW),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'columns' => [
                'user.username',
                [
                    'attribute' => 'noted_at',
                    'format' => 'datetime',
                    'enableSorting' => false,
                ],
            ],
        ]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-md-12">
        <?= $model->text_ready ?>
    </div>

</div>
