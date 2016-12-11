<?php

/* @var $this yii\web\View */
/* @var $epic Epic */

use common\models\Epic;
use common\models\Participant;
use yii\bootstrap\Html;
use yii\widgets\ListView;

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
            <?= Html::a(Yii::t('app', 'BUTTON_CHARACTERS'), ['character/index'],
                ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_CHARACTER_SHEETS'), ['character-sheet/index'],
                ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_GROUP'), ['group/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>

    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-md-8">

                <h2><?= Yii::t('app', 'EPIC_CARD_ANNOUNCEMENTS'); ?></h2>
                <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>

                <h2><?= Yii::t('app', 'EPIC_CARD_RECENT_EVENTS'); ?></h2>
                <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>

            </div>

            <div class="col-md-4">
                <h2><?= Yii::t('app', 'EPIC_CARD_EPIC_ATTRIBUTES'); ?></h2>

                <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>

                <h2><?= Yii::t('app', 'EPIC_CARD_TODO'); ?></h2>

                <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>

                <h2><?= Yii::t('app', 'EPIC_CARD_PARTICIPANTS'); ?></h2>

                <?= ListView::widget([
                    'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $epic->getParticipants()]),
                    'itemOptions' => ['class' => 'item'],
                    'layout' => '{items}',
                    'itemView' => function (Participant $model, $key, $index, $widget) {
                        return '<p>' . $model->user->username . ' (' . implode(', ', $model->getRolesList()) . ')</p>';
                    }
                ]) ?>
            </div>

        </div>

    </div>
</div>
