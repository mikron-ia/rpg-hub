<?php

/* @var $this yii\web\View */
/* @var $recaps \common\models\Recap[] */
/* @var $sessions \yii\data\ActiveDataProvider */

/* @var $stories \yii\data\ActiveDataProvider */

use yii\widgets\ListView;

?>
<div class="site-index">
    <div class="jumbotron">

        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>

    </div>

    <div class="col-lg-3 col-md-4">
        <h1 class="text-center"><?= Yii::t('app', 'FRONTEND_FRONT_PAGE_MAIN_SELECT_EPIC'); ?></h1>
        <?= $this->render('../_epic-selection_box', isset($objectEpic) ? ['objectEpic' => $objectEpic] : []) ?>
    </div>

    <div class="col-lg-5 col-md-4">
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
                            '../site/_recap_box',
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
                            '../story/_front_index_box',
                            ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                        );
                    },
                ]) ?>
            <?php else: ?>
                <p class="error-box"><?= Yii::t('app', 'FRONTPAGE_STORIES_NOT_AVAILABLE') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4 col-md-4">
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
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_NEWS_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_NEWS') ?>
            </h3>
            <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>
        </div>
    </div>
</div>
