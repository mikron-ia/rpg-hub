<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Scenario */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SCENARIO_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a(
                Yii::t('app', 'BUTTON_DELETE'),
                ['delete', 'id' => $model->scenario_id],
                [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'id' => $model->scenario_id],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'epic_id',
                    'format' => 'raw',
                    'value' => Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []),
                ],
                'key',
                'name',
                'tag_line',
            ],
        ]) ?>
    </div>

</div>
