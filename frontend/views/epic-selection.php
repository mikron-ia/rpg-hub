<?php

/* @var $this yii\web\View */
/* @var $objectEpic \common\models\Epic */

$this->title = 'RPG hub';

?>

<div class="site-index">
    <div class="jumbotron">
        <h1><?= Yii::t('app', 'FRONTEND_FRONT_PAGE_TITLE'); ?></h1>
        <p class="lead text-center">
            <?php if (empty($items)): ?>
                <?= Yii::t('app', 'FRONTEND_FRONT_PAGE_MAIN_EMPTY_EPIC_LIST'); ?>
            <?php else: ?>
                <?= Yii::t('app', 'FRONTEND_FRONT_PAGE_MAIN_SELECT_EPIC'); ?>
            <?php endif; ?>
        </p>
        <?= $this->render('_epic-selection_box', isset($objectEpic) ? ['objectEpic' => $objectEpic] : []) ?>
    </div>
</div>