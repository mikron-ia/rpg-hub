<?php

/* @var $this yii\web\View */
/* @var $epics Epic[] */
/* @var $recaps Recap[] */
/* @var $announcements ActiveDataProvider */
/* @var $sessions ActiveDataProvider */

/* @var $stories ActiveDataProvider */

use common\models\Epic;
use common\models\Recap;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

$this->title = Yii::t('app', 'FRONTPAGE_TITLE');
?>
<div class="site-index">
    <div class="text-center">
        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>
        <?= $this->render('../_epic-selection_box', ['epics' => $epics]) ?>
    </div>

    <div class="col-md-6">
        <h2 class="text-center" title="<?= Yii::t('app', 'FRONTPAGE_IC_TITLE_TEXT') ?>">
            <?= Yii::t('app', 'FRONTPAGE_IC') ?>
        </h2>
        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?>
            </h3>
            <?php if ($recaps): ?>
                <?= ListView::widget([
                    'dataProvider' => $recaps,
                    'emptyText' => '<p class="error-box">' . Yii::t('app', 'FRONTPAGE_RECAPS_NOT_AVAILABLE') . '</p>',
                    'layout' => '{items}',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../recap/_site_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                ]) ?>
            <?php else: ?>
                <p class="error-box"><?= Yii::t('app', 'FRONTPAGE_RECAPS_NOT_AVAILABLE') ?></p>
            <?php endif; ?>
        </div>
        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_STORIES_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_STORIES') ?>
            </h3>

            <?php if ($stories): ?>
                <?= ListView::widget([
                    'dataProvider' => $stories,
                    'emptyText' => '<p class="error-box">' . Yii::t('app', 'FRONTPAGE_STORIES_NOT_AVAILABLE') . '</p>',
                    'layout' => '{items}',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../story/_index_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                ]) ?>
            <?php else: ?>
                <p class="error-box"><?= Yii::t('app', 'FRONTPAGE_STORIES_NOT_AVAILABLE') ?></p>
            <?php endif; ?>
        </div>
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
                            '../session/_index_box',
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
            </div>

            <?php if ($announcements->count > 0): ?>
                <?= ListView::widget([
                    'dataProvider' => $announcements,
                    'emptyText' =>
                        '<p class="error-box">'
                        . Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT_NOT_AVAILABLE')
                        . '</p>',
                    'layout' => '{items}',
                    'separator' => '<hr />',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../announcement/_site_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                ]) ?>
            <?php else: ?>
                <p class="no-data-box"><?= Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT_NOT_AVAILABLE') ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
