<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Character */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Characters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(
            Yii::t('app', 'BUTTON_UPDATE'),
            ['update', 'id' => $model->character_id],
            ['class' => 'btn btn-primary']
        ); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_DELETE'),
            ['delete', 'id' => $model->character_id],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'key',
            ],
            [
                'attribute' => 'epic_id',
                'format' => 'raw',
                'value' => Html::a($model->epic->name, ['epic/view', 'id' => $model->epic_id], []),
            ],
            [
                'label' => Yii::t('app', 'LABEL_DATA_SIZE'),
                'format' => 'shortSize',
                'value' => strlen($model->data),
            ],
            [
                'attribute' => 'currently_delivered_person_id',
                'format' => 'raw',
                'value' => isset($model->currently_delivered_person_id) ?
                    Html::a(
                        $model->currentlyDeliveredPerson->name,
                        ['person/view', 'id' => $model->currently_delivered_person_id]
                    ) :
                    null,
            ],
        ],
    ]) ?>

</div>
