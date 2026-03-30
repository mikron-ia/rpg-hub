<?php

use common\models\core\Visibility;
use common\models\Story;
use frontend\assets\StoryAsset;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

StoryAsset::register($this);

/* @var $this View */
/* @var $model Story */
/* @var $storyCharacterPublic array<string> */
/* @var $storyCharacterPrivate array<string> */
/* @var $storyGroupPublic array<string> */
/* @var $storyGroupPrivate array<string> */
/* @var $showPrivates bool */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'STORY_TITLE_INDEX'),
    'url' => ['index', 'key' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $showPrivates;

$items = [
    [
        'label' => Yii::t('app', 'STORY_SHORT_TAB'),
        'content' => $this->render('_view_short', ['model' => $model]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'STORY_LONG_TAB'),
        'content' => $this->render('_view_long', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ],
    [
        'label' => Yii::t('app', 'STORIES_ASSIGNMENT_ACTORS_TAB'),
        'content' => $this->render('_view_actors_assigned', [
            'model' => $model,
            'storyCharacterPublic' => $storyCharacterPublic,
            'storyCharacterPrivate' => $storyCharacterPrivate,
            'storyGroupPublic' => $storyGroupPublic,
            'storyGroupPrivate' => $storyGroupPrivate,
            'showPrivateWarning' => $showPrivates,
        ]),
        'encode' => false,
        'active' => false,
    ],
];

if ($showPrivates) {
    $items[] = [
        'label' => Yii::t('app', 'CHARACTER_GM_TAB'),
        'content' => $this->render('_view_gm', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ];
}
?>
<div class="story-view">
    <h1>
        <?php if ($model->story_id === $model->epic->current_story_id): ?>
            <span class="current-tag tag-view-page"><?= Yii::t('app', 'TAG_CURRENT_F') ?></span>
        <?php endif; ?>
        <?php if ($model->hasCodeName()): ?>
            <span class="type-tag tag-view-page"><?= $model->getCodeName() ?></span>
        <?php endif; ?>
        <?php if ($model->getVisibility() !== Visibility::VISIBILITY_FULL): ?>
            <span class="unpublished-tag tag-view-page"><?= Yii::t('app', 'TAG_UNPUBLISHED_F') ?></span>
        <?php endif; ?>
        <?= Html::encode($this->title) ?>
    </h1>
    <?= Tabs::widget(['items' => $items]) ?>
</div>
