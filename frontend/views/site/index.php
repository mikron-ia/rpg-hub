<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = 'RPG hub - index';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>
        <p class="lead"><?= Yii::t('app', 'FRONTPAGE_LEAD_TEXT') ?></p>
    </div>

    <div class="col-md-3">
        <h2 class="text-center"><?= Yii::t('app', 'FRONTPAGE_MENU') ?></h2>
        <div>
            <p>
                <?= Html::a(
                    Yii::t('app', 'BUTTON_FRONTPAGE_STORIES'),
                    ['story/index'],
                    ['class' => 'btn btn-lg btn-success']
                ); ?>
            </p>
            <p>
                <?= Html::a(
                    Yii::t('app', 'BUTTON_FRONTPAGE_PEOPLE'),
                    ['people/index'],
                    ['class' => 'btn btn-lg btn-success']
                ); ?>
            </p>
        </div>
    </div>
    <div class="col-md-3">

    </div>
</div>
