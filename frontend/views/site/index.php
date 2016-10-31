<?php

/* @var $this yii\web\View */
/* @var $epic \common\models\Epic */
/* @var $stories \yii\data\ActiveDataProvider */

use yii\bootstrap\Html;
use yii\widgets\ListView;

$this->title = 'RPG hub - index';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>
        <?php if ($epic): ?>

            <div class="btn-group btn-group-lg">
                <?= Html::a(Yii::t('app', 'BUTTON_STORIES'), ['story/index'], ['class' => 'btn btn-lg btn-success']); ?>
            </div>

            <div class="btn-group btn-group-lg">
                <?= Html::a(Yii::t('app', 'BUTTON_PEOPLE'), ['person/index'], ['class' => 'btn btn-lg btn-success']); ?>
            </div>

        <?php else: ?>
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
                <p><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></p>
            </div>
            <div>
                <h3 title="<?= Yii::t('app', 'FRONTPAGE_STORIES_TITLE_TEXT') ?>">
                    <?= Yii::t('app', 'FRONTPAGE_STORIES') ?>
                </h3>
                <?= ListView::widget([
                    'dataProvider' => $stories,
                    'summary' => '',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('story/_index_box', ['model' => $model]);
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

            <p><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></p>
        </div>

        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_NEWS_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_NEWS') ?>
            </h3>
            <p><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></p>
        </div>

    </div>
</div>
