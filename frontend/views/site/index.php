<?php

use common\models\Epic;
use yii\data\DataProviderInterface;
use yii\web\View;
use yii\widgets\ListView;

/* @var $this View */
/* @var $epics Epic[] */

/* @var $announcements DataProviderInterface */
/* @var $projects DataProviderInterface */
/* @var $recaps DataProviderInterface */
/* @var $sessions DataProviderInterface */
/* @var $stories DataProviderInterface */

$this->title = Yii::t('app', 'FRONTPAGE_TITLE');
?>
<div class="site-index">
    <div class="text-center">
        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>
        <?= $this->render('../_epic-selection_box', ['epics' => $epics]) ?>
    </div>

    <?php if (empty($epics)): ?>
        <div class="lead welcome-box">
            <?= Yii::t('app', 'FRONTEND_FRONT_PAGE_MAIN_EMPTY_EPIC_LIST') ?>
        </div>
    <?php endif; ?>

    <div class="col-md-8">
        <?php if (!empty($epics)): ?>
            <h2 class="text-center" title="<?= Yii::t('app', 'FRONTPAGE_IC_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_IC') ?>
            </h2>
        <?php endif; ?>
        <?php if ($recaps->count > 0): ?>
            <div>
                <h3 title="<?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT') ?>">
                    <?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?>
                </h3>

                <?= ListView::widget([
                    'dataProvider' => $recaps,
                    'layout' => '{items}',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../recap/_site_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                    'separator' => '<hr />',
                ]) ?>
            </div>
        <?php endif; ?>
        <?php if ($stories->count > 0): ?>
            <div>
                <h3 title="<?= Yii::t('app', 'FRONTPAGE_CURRENT_STORIES_TITLE_TEXT') ?>">
                    <?= Yii::t('app', 'FRONTPAGE_CURRENT_STORIES') ?>
                </h3>

                <?= ListView::widget([
                    'dataProvider' => $stories,
                    'layout' => '{items}',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render(
                            '../story/_index_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                    'separator' => '<hr />',
                ]) ?>
            </div>
        <?php endif; ?>
        <?php if ($projects->count > 0): ?>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_PROJECTS_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_PROJECTS') ?>
            </h3>

            <?= ListView::widget([
                'dataProvider' => $projects,
                'layout' => '{items}',
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render(
                        '../project/_index_box',
                        ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                    );
                },
                'separator' => '<hr />',
            ]) ?>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <?php if (!empty($epics)): ?>
            <h2 class="text-center" title="<?= Yii::t('app', 'FRONTPAGE_OOC_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_OOC') ?>
            </h2>
        <?php endif; ?>
        <?php if ($sessions->count > 0): ?>
            <div>
                <h3 title="<?= Yii::t('app', 'FRONTPAGE_SESSIONS_TITLE_TEXT') ?>">
                    <?= Yii::t('app', 'FRONTPAGE_SESSIONS') ?>
                </h3>
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
            </div>
        <?php endif; ?>
        <?php if ($announcements->count > 0): ?>
            <div>
                <div class="buttoned-header">
                    <h3 title="<?= Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT_TITLE_TEXT') ?>">
                        <?= Yii::t('app', 'FRONTPAGE_ANNOUNCEMENT') ?>
                    </h3>
                </div>

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
            </div>
        <?php endif; ?>
    </div>
</div>
