<?php

use common\models\Epic;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EpicQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_EPICS_MANAGEMENT');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_EPIC_ADD'), ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterPosition' => null,
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'name',
            ],
            [
                'attribute' => 'system',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{action}',
                'buttons' => [
                    'action' => function ($url, Epic $model, $key) {
                        /** @var User $user */
                        $user = Yii::$app->user->identity;

                        if ($model->isUserYourManager($user)) {
                            $action = 'epic/manager-detach';
                            $confirmationText = Yii::t('app', 'MANAGE_EPIC_CONFIRMATION_DETACH');
                            $glyph = 'glyphicon-log-out';
                            $label = Yii::t('app', 'MANAGE_EPIC_LABEL_DETACH');
                        } else {
                            $action = 'epic/manager-attach';
                            $confirmationText = Yii::t('app', 'MANAGE_EPIC_CONFIRMATION_ATTACH');
                            $glyph = 'glyphicon-log-in';
                            $label = Yii::t('app', 'MANAGE_EPIC_LABEL_ATTACH');
                        }

                        return Html::a('<span class="glyphicon ' . $glyph . '"></span>',
                            [$action, 'id' => $key],
                            [
                                'title' => $label,
                                'data-confirm' => $confirmationText,
                                'data-method' => 'post',
                            ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
