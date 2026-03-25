<?php

use common\models\Epic;
use yii\web\View;

/* @var $this View */
/* @var $objectEpic Epic */

$this->title = 'RPG hub';

?>

<div class="site-index">
    <div class="jumbotron">
        <h1><?= Yii::t('app', 'FRONTEND_FRONT_PAGE_TITLE'); ?></h1>
        <p class="lead text-center">
            <?= empty($items)
                ? Yii::t('app', 'FRONTEND_FRONT_PAGE_MAIN_SELECT_EPIC')
                : Yii::t('app', 'FRONTEND_FRONT_PAGE_MAIN_EMPTY_EPIC_LIST');
            ?>
        </p>
        <?= $this->render('_epic-selection_box', isset($objectEpic) ? ['objectEpic' => $objectEpic] : []) ?>
    </div>
</div>
