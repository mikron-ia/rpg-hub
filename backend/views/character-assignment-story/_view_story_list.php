<?php

use common\models\Character;
use common\models\core\Visibility;
use common\models\StoryCharacterAssignment;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $model Character */
/* @var $dataProvider ArrayDataProvider */
?>

<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'story.name',
            ],
            [
                'attribute' => 'story.visibility',
                'label' => Yii::t('app', 'LABEL_VISIBILITY_OBJECT'),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => fn(StoryCharacterAssignment $model) => (Visibility::tryFrom($model->story->visibility))?->getName(),
                'enableSorting' => false,
            ],
            [
                'attribute' => 'visibility',
                'label' => Yii::t('app', 'LABEL_VISIBILITY_ASSIGNMENT'),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => fn(StoryCharacterAssignment $model) => (Visibility::tryFrom($model->visibility))?->getName(),
                'enableSorting' => false,
            ],
        ],
    ]); ?>
</div>
