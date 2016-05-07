<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'STORY_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-right">
        <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->story_id],
            ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'key',
            'data:ntext',
        ],
    ]) ?>

    <h2><?php echo $model->getAttributeLabel('short'); ?></h2>

    <div>
        <?php echo $model->short; ?>
    </div>

    <h2><?php echo $model->getAttributeLabel('long'); ?></h2>

    <div>
        <?php echo $model->long; ?>
    </div>

    <h2><?php echo $model->getAttributeLabel('storyParameters'); ?></h2>

    <p class="text-right">
        <?= Html::a(Yii::t('app', 'BUTTON_STORY_PARAMETER_CREATE'), ['create-parameter'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => \common\models\StoryParameter::find()->with('story')->where(['story_id' => $model->story_id])]),
        'summary' => '',
        'columns' => [
            'code',
            'content',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update-parameter}',
                'buttons' => [
                    'update-parameter' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>

</div>
