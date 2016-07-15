<?php

/* @var $this yii\web\View */
/* @var $epic Epic */

use common\models\Epic;
use yii\bootstrap\Html;

$this->title = 'RPG hub - control';
?>
<div class="site-index">

    <div class="text-center">

        <h1><?= $epic->name ?></h1>

        <div class="btn-group btn-group-lg">
            <?= Html::a(
                Yii::t('app', 'BUTTON_DETAILS'),
                ['epic/view', 'id' => $epic->epic_id],
                ['class' => 'btn btn-lg btn-success'])
            ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(Yii::t('app', 'BUTTON_STORIES'), ['story/index'], ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_RECAPS'), ['recap/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(Yii::t('app', 'BUTTON_PEOPLE'), ['person/index'], ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_CHARACTERS'), ['character/index'], ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_GROUP'), ['group/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>

    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-md-8">
                <h2><?= Yii::t('app', 'EPIC_CARD_RECENT_EVENTS'); ?></h2>
            </div>

            <div class="col-md-4">
                <p>[attributes]</p>
                <p>[todo for GM]</p>
            </div>

        </div>

    </div>
</div>
