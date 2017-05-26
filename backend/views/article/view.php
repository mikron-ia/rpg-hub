<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'id' => $model->article_id],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'id' => $model->article_id],
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
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => $model->epic_id
                        ? (Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []))
                        : Yii::t('app', 'ARTICLE_NO_EPIC'),
                ],
                'key',
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibility()
                ],
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
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

    <div class="col-md-12">
        <?= $model->text_ready ?>
    </div>

</div>
