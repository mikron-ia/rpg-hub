<?php

use common\models\Story;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'STORY_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'BUTTON_STORY_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'key',
            'name',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, Story $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            $url,
                            ['title' => Yii::t('app', 'BUTTON_VIEW')]
                        );
                    },
                    'update' => function ($url, Story $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            $url,
                            ['title' => Yii::t('app', 'BUTTON_UPDATE')]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

</div>
