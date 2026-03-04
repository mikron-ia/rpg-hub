<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
?>

<div class="col-md-6">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'key',
            ]
        ],
    ]) ?>

    <div class="text-center">
        <?= Html::a(
            Yii::t('app', 'BUTTON_SEE_BACKEND'),
            Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl(['story/view', 'key' => $model->key]),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

</div>
