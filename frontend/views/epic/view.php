<?php

/* @var $this yii\web\View */
/* @var $epic \common\models\Epic */
/* @var $sessions \yii\data\ActiveDataProvider */
/* @var $stories \yii\data\ActiveDataProvider */

/* @var $recap \common\models\Recap */

use yii\bootstrap\Html;
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
                    ['story/index'],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
            </div>

            <div class="btn-group btn-group-lg">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_CHARACTERS'),
                    ['character/index'],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
                <?= Html::a(
                    Yii::t('app', 'BUTTON_GROUP'),
                    ['group/index'],
                    ['class' => 'btn btn-lg btn-primary']
                ); ?>
            </div>

            <div class="btn-group btn-group-lg">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_CHARACTER_SHEETS'),
                    ['character-sheet/index'],
                    ['class' => 'btn btn-primary']
                ); ?>
            </div>

            <div class="btn-group btn-group-lg">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_ARTICLES'),
                    ['article/index'],
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
            <div>
                <h3 title="<?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT') ?>">
                    <?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?>
                </h3>
                <div>
                    <?php if ($recap) {
                        if ($recap->point_in_time_id) {
                            echo '<p class="recap-box-time">' . $recap->pointInTime . '</p>';
                        }
                        echo $recap->getDataFormatted();
                    } else {
                        echo '<p class="error-box">' . Yii::t('app', 'FRONTPAGE_RECAP_NOT_AVAILABLE') . '</p>';
                    } ?>
                </div>
            </div>

            <div>
                <div class="buttoned-header">
                    <h3 title="<?= Yii::t('app', 'FRONTPAGE_STORIES_TITLE_TEXT') ?>">
                        <?= Yii::t('app', 'FRONTPAGE_STORIES') ?>
                    </h3>
                    <?= Html::a(Yii::t('app', 'BUTTON_STORY_VIEW_ALL'), ['story/index'],
                        ['class' => 'btn btn-primary']); ?>
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
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_NEWS_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_NEWS') ?>
            </h3>
            <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>
        </div>

        <h3><?= Yii::t('app', 'EPIC_CARD_EPIC_ATTRIBUTES'); ?></h3>
        <span class="epic-status <?= $epic->getStatusClass(); ?>"><?= $epic->getStatus(); ?></span>

    </div>

</div>
