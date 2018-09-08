<?php

/* @var $this yii\web\View */
/* @var $objectEpic \common\models\Epic */

$this->title = 'RPG hub - control';

?>

<div class="site-index">
    <div class="jumbotron">
        <h1><?= Yii::t('app', 'BACKEND_FRONT_PAGE_TITLE'); ?></h1>
        <p class="lead text-center"><?= Yii::t('app', 'BACKEND_FRONT_PAGE_MAIN_SELECT_EPIC'); ?></p>
        <?= $this->render('_epic-selection_box', isset($objectEpic) ? ['objectEpic' => $objectEpic] : []) ?>
    </div>
</div>