<?php

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
                    'format' => 'raw',
                    'label' => Yii::t('app', 'LABEL_EPIC'),
                    'value' => Html::a($model->epic->name, ['epic/view', 'key' => $model->epic->key], []),
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
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

</div>
