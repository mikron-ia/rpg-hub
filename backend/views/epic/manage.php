<?php

use common\models\Epic;
use common\models\EpicQuery;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel EpicQuery */
/* @var $dataProvider ActiveDataProvider */

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
                'label' => Yii::t('app', 'MANAGE_EPIC_TAGS'),
                'format' => 'raw',
                'value' => function (Epic $model) {
                    $tags = [];

                    /** @var User $user */
                    $user = Yii::$app->user->identity;

                    if ($model->isUserYourManager($user)) {
                        $tags[] = '<span class="table-tag managed-tag">'
                            . Yii::t('app', 'MANAGE_EPIC_TAG_MANAGED')
                            . '</span>';
                    }

                    return implode($tags);
                },
            ],
            [
                'class' => ActionColumn::class,
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

                        return Html::a(
                            '<span class="glyphicon ' . $glyph . '"></span>',
                            [$action, 'key' => $model->key],
                            [
                                'title' => $label,
                                'data-confirm' => $confirmationText,
                                'data-method' => 'post',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
