<?php

use common\models\core\Visibility;
use common\models\Story;
use common\models\StoryCharacterAssignment;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $model Story */
/* @var $dataProvider ArrayDataProvider */
?>

<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '<span class="assignment-summary">' . Yii::t('app', 'ASSIGNMENT_SUMMARY {totalCount}') . '</span>',
        'columns' => [
            [
                'attribute' => 'character.name',
                'format' => 'raw',
                'value' => fn(StoryCharacterAssignment $model) => (string)$model->character,
            ],
            [
                'attribute' => 'rank',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
                'value' => fn(StoryCharacterAssignment $model) => $model->getRank()->getName(),
            ],
            [
                'attribute' => 'character.visibility',
                'label' => Yii::t('app', 'LABEL_VISIBILITY_OBJECT'),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => fn(StoryCharacterAssignment $model) => (Visibility::tryFrom($model->character->visibility))?->getName(),
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
