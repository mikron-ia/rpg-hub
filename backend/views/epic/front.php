<?php

/* @var $this yii\web\View */
/* @var $epic Epic */
/* @var $sessions ActiveDataProvider */
/* @var $stories ActiveDataProvider */

/* @var $recap Recap */

use common\models\Epic;
use common\models\Participant;
use common\models\Recap;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

$this->title = Yii::t('app', 'FRONTPAGE_TITLE');
?>
<div class="site-index">

    <div class="text-center">

        <h1><?= $epic->name ?></h1>

        <div class="btn-group btn-group-lg">
            <?= Html::a(Yii::t('app', 'BUTTON_STORIES'),
                ['story/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary']
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_RECAPS'),
                ['recap/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary']
            ); ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(
                Yii::t('app', 'BUTTON_CHARACTERS'),
                ['character/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_CHARACTER_SHEETS'),
                ['character-sheet/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_GROUP'),
                ['group/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(
                Yii::t('app', 'BUTTON_DETAILS'),
                ['epic/view', 'key' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SESSIONS'),
                ['game/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SCENARIOS'),
                ['scenario/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_POINTS_IN_TIME'),
                ['point-in-time/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(
                Yii::t('app', 'BUTTON_ARTICLES'),
                ['article/index', 'epic' => $epic->key],
                ['class' => 'btn btn-lg btn-primary'],
            ); ?>
        </div>

    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-md-8">

                <h2><?= Yii::t('app', 'EPIC_CARD_ANNOUNCEMENTS'); ?></h2>
                <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>

                <h2><?= Yii::t('app', 'EPIC_CARD_RECENT_EVENTS'); ?></h2>
                <div>
                    <?php if ($recap) {
                        if ($recap->point_in_time_id) {
                            echo '<p class="recap-box-time">' . $recap->pointInTime . '</p>';
                        }
                        echo $recap->getDataFormatted();
                    } else {
                        echo '<p class="error-box">' . Yii::t('app', 'FRONTPAGE_RECAP_NOT_AVAILABLE') . '</p>';
                    } ?>
                </div>

                <h2 title="<?= Yii::t('app', 'FRONTPAGE_STORIES_TITLE_TEXT') ?>">
                    <?= Yii::t('app', 'FRONTPAGE_STORIES') ?>
                </h2>

                <div>
                    <?= ListView::widget([
                        'dataProvider' => $stories,
                        'layout' => '{items}',
                        'itemOptions' => ['class' => 'item'],
                        'itemView' => function ($model, $key, $index, $widget) {
                            return $this->render(
                                'story/_index_box',
                                ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget]
                            );
                        },
                    ]) ?>
                </div>

            </div>

            <div class="col-md-4">
                <h2><?= Yii::t('app', 'EPIC_CARD_EPIC_ATTRIBUTES'); ?></h2>

                <span class="epic-status <?= $epic->getStatusClass(); ?>"><?= $epic->getStatus(); ?></span>

                <h2><?= Yii::t('app', 'EPIC_CARD_SESSIONS'); ?></h2>

                <div>
                    <?= ListView::widget([
                        'dataProvider' => $sessions,
                        'emptyText' => '<p class="error-box">'
                            . Yii::t('app', 'FRONTPAGE_SESSION_NOT_AVAILABLE')
                            . '</p>',
                        'layout' => '{items}',
                        'itemOptions' => ['class' => 'item'],
                        'itemView' => function ($model, $key, $index, $widget) {
                            return $this->render(
                                'session/_index_box',
                                ['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget],
                            );
                        },
                    ]) ?>
                </div>

                <h2><?= Yii::t('app', 'EPIC_CARD_TODO'); ?></h2>

                <p><i><?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?></i></p>

                <h2><?= Yii::t('app', 'EPIC_CARD_PARTICIPANTS'); ?></h2>

                <?= ListView::widget([
                    'dataProvider' => new ActiveDataProvider(['query' => $epic->getParticipants()]),
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
