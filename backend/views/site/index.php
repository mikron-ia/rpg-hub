<?php

/* @var $this yii\web\View */

$this->title = 'RPG hub - control';
?>
<div class="site-index">

    <div class="jumbotron">

        <h1>RPG Hub Control</h1>

        <p class="lead"><?= Yii::t('app', 'BACKEND_FRONT_PAGE_MAIN_TEXT'); ?></p>

        <p class="btn-group btn-group-lg">
            <a class="btn btn-lg btn-success" href="index.php/story/"><?= Yii::t('app', 'BUTTON_STORIES'); ?></a>
            <a class="btn btn-lg btn-success" href="index.php/recap/"><?= Yii::t('app', 'BUTTON_RECAPS'); ?></a>
            <a class="btn btn-lg btn-success" href="index.php/character/"><?= Yii::t('app', 'BUTTON_CHARACTERS'); ?></a>
            <a class="btn btn-lg btn-success" href="index.php/person/"><?= Yii::t('app', 'BUTTON_PEOPLE'); ?></a>
        </p>

    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-lg-6">
                <h2><?= Yii::t('app', 'CONFIGURATION_TITLE_INDEX'); ?></h2>

                <p><?= Yii::t('app', 'CONFIGURATION_FRONTPAGE_TEXT'); ?></p>

                <p class="text-center">
                    <a class="btn btn-default btn-block" href="index.php/epic/"><?= Yii::t('app', 'BUTTON_EPICS'); ?></a>
                </p>
                <p class="text-center">
                    <a class="btn btn-default btn-block" href="#"><?= Yii::t('app', 'BUTTON_CHANGE_EPIC'); ?> &raquo;</a>
                </p>
            </div>

        </div>

    </div>
</div>
