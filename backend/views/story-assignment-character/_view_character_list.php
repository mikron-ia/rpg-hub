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
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'character.name',
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
