<?php

use common\models\ExternalData;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Scenario */
/* @var $externalDataDataProvider yii\data\ActiveDataProvider */

?>

<div>

    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_BASIC_DATA_AND_OPERATIONS'); ?></h2>
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

        <div class="text-center">
            <?= Html::a(Yii::t('app', 'BUTTON_LOAD'), ['load-data', 'id' => $model->scenario_id], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_LOAD'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'id' => $model->scenario_id],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_DELETE'), ['delete', 'id' => $model->scenario_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_DELETE'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

</div>
