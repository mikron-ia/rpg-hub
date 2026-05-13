<?php

use common\models\Group;
use common\models\core\Visibility;
use common\models\StoryGroupAssignment;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $model Group */
/* @var $dataProvider ArrayDataProvider */
?>

<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '<span class="assignment-summary">' . Yii::t('app', 'ASSIGNMENT_SUMMARY {totalCount}') . '</span>',
        'columns' => [
            [
                'attribute' => 'story.name',
                'format' => 'raw',
                'value' => fn(StoryGroupAssignment $model) => (string)$model->story,
            ],
            [
                'attribute' => 'rank',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
                'value' => fn(StoryGroupAssignment $model) => $model->getRank()->getName(),
            ],
            [
                'attribute' => 'story.visibility',
                'label' => Yii::t('app', 'LABEL_VISIBILITY_OBJECT'),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => fn(StoryGroupAssignment $model) => (Visibility::tryFrom($model->story->visibility))?->getName(),
                'enableSorting' => false,
            ],
            [
                'attribute' => 'visibility',
                'label' => Yii::t('app', 'LABEL_VISIBILITY_ASSIGNMENT'),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => fn(StoryGroupAssignment $model) => (Visibility::tryFrom($model->visibility))?->getName(),
                'enableSorting' => false,
            ],
        ],
    ]); ?>
</div>
