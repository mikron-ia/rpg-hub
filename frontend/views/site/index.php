<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = 'RPG hub - index';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>
        <p class="lead"><?= Yii::t('app', 'FRONTPAGE_LEAD_TEXT') ?></p>
        <div class="btn-group btn-group-lg">
            <?= Html::a(Yii::t('app', 'BUTTON_STORIES'), ['story/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(Yii::t('app', 'BUTTON_PEOPLE'), ['person/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'FRONTPAGE_IC') ?></h2>
        <h3><?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?></h3>
    </div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'FRONTPAGE_OOC') ?></h2>
        <h3><?= Yii::t('app', 'FRONTPAGE_SESSIONS') ?></h3>
        <h3><?= Yii::t('app', 'FRONTPAGE_NEWS') ?></h3>
    </div>
</div>
