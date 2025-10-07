<?php

/* @var $this yii\web\View */
/* @var $epic Epic */
/* @var $sessions ActiveDataProvider */
/* @var $stories ActiveDataProvider */
/* @var $announcements ActiveDataProvider */
/* @var $showScenarios bool */

/* @var $recap Recap */

use common\models\Epic;
use common\models\Recap;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

if ($epic) {
    $this->title = $epic->name . ' - ' . Yii::t('app', 'FRONTPAGE_TITLE');
} else {
    $this->title = Yii::t('app', 'FRONTPAGE_TITLE');
}

?>
<div class="epic-view">

    <div class="text-center">

        <?php if ($epic): ?>

            <h1><?= $epic->name ?></h1>

            <div class="btn-group btn-group-lg">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_STORIES'),
                    ['story/index', 'key' => $epic->key],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
                <?= Html::a(
                    Yii::t('app', 'BUTTON_RECAPS'),
                    ['recap/index', 'key' => $epic->key],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
            </div>

            <div class="btn-group btn-group-lg">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_CHARACTERS'),
                    ['character/index', 'key' => $epic->key],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
                <?= Html::a(
                    Yii::t('app', 'BUTTON_GROUP'),
                    ['group/index', 'key' => $epic->key],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
            </div>

            <div class="btn-group btn-group-lg">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_CHARACTER_SHEETS'),
                    ['character-sheet/index', 'key' => $epic->key],
                    ['class' => 'btn btn-primary']
                ); ?>
            </div>

            <?php if ($showScenarios): ?>
                <div class="btn-group btn-group-lg">
                    <?= Html::a(
                        Yii::t('app', 'BUTTON_SCENARIOS'),
                        ['scenario/index', 'key' => $epic->key],
                        ['class' => 'btn btn-lg btn-primary']
                    ); ?>
                </div>
            <?php endif; ?>

            <div class="btn-group btn-group-lg">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_ARTICLES'),
                    ['article/index', 'key' => $epic->key],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
            </div>

        <?php else: ?>
            <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>
            <p class="error-box"><?= Yii::t('app', 'ERROR_NO_EPIC_ACTIVE_FRONTPAGE_BUTTONS') ?></p>
        <?php endif; ?>

    </div>

    <div class="col-md-6">

        <h2 class="text-center" title="<?= Yii::t('app', 'FRONTPAGE_IC_TITLE_TEXT') ?>">
            <?= Yii::t('app', 'FRONTPAGE_IC') ?>
        </h2>

        <?php if ($epic): ?>
            <?php if (isset($epic->current_story_id)): ?>
                <h3><?= Yii::t('app', 'EPIC_CURRENT_STORY'); ?></h3>

                <span><?= $epic->getCurrentStory()->one(); ?></span>
            <?php endif; ?>

            <div>
                <?= $this->render('../recap/_epic_box', ['model' => $recap]) ?>
            </div>

            <div>
                <div class="buttoned-header">
                    <h3 title="<?= Yii::t('app', 'FRONTPAGE_STORIES_TITLE_TEXT') ?>">
                        <?= Yii::t('app', 'FRONTPAGE_STORIES') ?>
                    </h3>
                    <?= Html::a(
                        Yii::t('app', 'BUTTON_STORY_VIEW_ALL'),
                        ['story/index', 'key' => $epic->key],
                        ['class' => 'btn btn-primary']
                    ); ?>
                </div>
                <?= ListView::widget([
                    'dataProvider' => $stories,
                    'emptyText' => '<p class="error-box">' . Yii::t('app', 'FRONTPAGE_STORIES_NOT_AVAILABLE') . '</p>',
                    'layout' => '{items}',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../story/_epic_box_short',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                ]) ?>
            </div>
        <?php else: ?>
            <p class="error-box"><?= Yii::t('app', 'ERROR_NO_EPIC_ACTIVE_FRONTPAGE_IC') ?></p>
        <?php endif; ?>

    </div>

    <div class="col-md-6">

        <h2 class="text-center" title="<?= Yii::t('app', 'FRONTPAGE_OOC_TITLE_TEXT') ?>">
            <?= Yii::t('app', 'FRONTPAGE_OOC') ?>
        </h2>

        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_SESSIONS_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_SESSIONS') ?>
            </h3>
            <?php if ($sessions): ?>
                <?= ListView::widget([
                    'dataProvider' => $sessions,
                    'emptyText' => '<p class="error-box">' . Yii::t('app', 'EPIC_SESSION_NOT_AVAILABLE') . '</p>',
                    'layout' => '{items}',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../session/_epic_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                ]) ?>
            <?php else: ?>
                <p class="error-box"><?= Yii::t('app', 'EPIC_SESSION_NOT_AVAILABLE') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <div class="buttoned-header">
                <h3 title="<?= Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT_TITLE_TEXT') ?>">
                    <?= Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT') ?>
                </h3>
                <?php if ($announcements->count > 0): ?>
                    <?= Html::a(
                        Yii::t('app', 'BUTTON_ANNOUNCEMENT_VIEW_ALL'),
                        ['announcement/index', 'key' => $epic->key],
                        ['class' => 'btn btn-primary']
                    ); ?>
                <?php endif; ?>
            </div>

            <?php if ($announcements->count > 0): ?>
                <?= ListView::widget([
                    'dataProvider' => $announcements,
                    'emptyText' => '<p class="error-box">' . Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT_NOT_AVAILABLE') . '</p>',
                    'layout' => '{items}',
                    'separator' => '<hr />',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../announcement/_epic_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                ]) ?>
            <?php else: ?>
                <p class="no-data-box"><?= Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT_NOT_AVAILABLE') ?></p>
            <?php endif; ?>
        </div>

        <h3><?= Yii::t('app', 'EPIC_CARD_EPIC_ATTRIBUTES'); ?></h3>
        <span class="epic-status <?= $epic->getStatusClass(); ?>"><?= $epic->getStatus(); ?></span>

    </div>

</div>
