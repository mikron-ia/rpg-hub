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
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => $model->getStatus(),
                ],
                'tag_line',
            ],
        ]) ?>

        <div class="text-center">
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'id' => $model->scenario_id],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

</div>
