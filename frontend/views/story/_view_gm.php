<?php

use common\models\Story;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Story */
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
