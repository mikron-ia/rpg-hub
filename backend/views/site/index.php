<?php

/* @var $this yii\web\View */
/* @var $epic Epic */

use common\models\Epic;
use common\models\Participant;
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
            <?= Html::a(Yii::t('app', 'BUTTON_PEOPLE'), ['character/index'], ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_CHARACTER_SHEETS'), ['character-sheet/index'],
                ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_GROUP'), ['group/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>

    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-md-8">
                <h2><?= Yii::t('app', 'EPIC_CARD_ANNOUNCEMENTS'); ?></h2>
                <h2><?= Yii::t('app', 'EPIC_CARD_RECENT_EVENTS'); ?></h2>
            </div>

            <div class="col-md-4">
                <h2><?= Yii::t('app', 'EPIC_CARD_EPIC_ATTRIBUTES'); ?></h2>

                <p>[attributes]</p>

                <h2><?= Yii::t('app', 'EPIC_CARD_TODO'); ?></h2>

                <p>[todo for the user]</p>

                <h2><?= Yii::t('app', 'EPIC_CARD_PARTICIPANTS'); ?></h2>

                <?php
                foreach ($epic->gms as $gm) {
                    /* @var $gm Participant */
                    echo '<p>' . $gm->user->username . ' (' . Yii::t('app', 'LABEL_GM') . ')</p>';
                }
                foreach ($epic->players as $player) {
                    /* @var $gm Participant */
                    echo '<p>' . $player->user->username . ' (' . Yii::t('app', 'LABEL_PLAYER') . ')</p>';
                }
                ?>
            </div>

        </div>

    </div>
</div>
