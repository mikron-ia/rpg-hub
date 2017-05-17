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

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->article_id],
            ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->article_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'article_id',
            [
                'attribute' => 'epic_id',
                'format' => 'raw',
                'value' => $model->epic_id
                    ? (Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []))
                    : Yii::t('app', 'ARTICLE_NO_EPIC'),
            ],
            'key',
            'title',
            'subtitle',
            [
                'attribute' => 'visibility',
                'value' => $model->getVisibility()
            ],
        ],
    ]) ?>

    <div>
        <?= $model->text_ready ?>
    </div>

</div>
