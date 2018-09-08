<?php

/* @var $this yii\web\View */
/* @var $epic \common\models\Epic */
/* @var $sessions \yii\data\ActiveDataProvider */
/* @var $stories \yii\data\ActiveDataProvider */

/* @var $recap \common\models\Recap */

use yii\widgets\ListView;

if ($epic) {
    $this->title = $epic->name . ' - ' . Yii::t('app', 'FRONTPAGE_TITLE');
} else {
    $this->title = Yii::t('app', 'FRONTPAGE_TITLE');
}

?>
<div class="site-index">

    <div class="jumbotron">

        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>

    </div>

    <div class="col-md-4">
        <h1 class="text-center"><?= Yii::t('app', 'FRONTEND_FRONT_PAGE_MAIN_SELECT_EPIC'); ?></h1>
        <?= $this->render('../_epic-selection_box', isset($objectEpic) ? ['objectEpic' => $objectEpic] : []) ?>
    </div>

    <div class="col-md-4">

        <h2 class="text-center" title="<?= Yii::t('app', 'FRONTPAGE_IC_TITLE_TEXT') ?>">
            <?= Yii::t('app', 'FRONTPAGE_IC') ?>
        </h2>

        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?>
            </h3>
            <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED_SEE_EPIC_PAGE') ?></i></p>
        </div>

        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_STORIES_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_STORIES') ?>
            </h3>
            <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED_SEE_EPIC_PAGE') ?></i></p>
        </div>

    </div>

    <div class="col-md-4">

        <h2 class="text-center" title="<?= Yii::t('app', 'FRONTPAGE_OOC_TITLE_TEXT') ?>">
            <?= Yii::t('app', 'FRONTPAGE_OOC') ?>
        </h2>


        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_SESSIONS_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_SESSIONS') ?>
            </h3>
            <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED_SEE_EPIC_PAGE') ?></i></p>
        </div>

        <div>
            <h3 title="<?= Yii::t('app', 'FRONTPAGE_NEWS_TITLE_TEXT') ?>">
                <?= Yii::t('app', 'FRONTPAGE_NEWS') ?>
            </h3>
            <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>
        </div>

    </div>

</div>
