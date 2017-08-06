<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $externalDataDataProvider yii\data\ActiveDataProvider */

?>

<div>

    <div class="col-md-6">

        <div class="clearfix">&nbsp;</div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
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
                    'attribute' => 'visibility',
                    'value' => $model->getVisibility(),
                ],
                [
                    'attribute' => 'importance_category',
                    'value' => $model->getImportanceCategory(),
                ],
            ],
        ]) ?>

        <div class="text-center">
            <?= Html::a(Yii::t('app', 'BUTTON_LOAD'), ['load-data', 'key' => $model->key], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => Yii::t('app', 'CONFIRMATION_LOAD'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= \yii\helpers\Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'group/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>

</div>
