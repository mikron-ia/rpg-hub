<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Character */
?>

<div class="col-md-6">

    <?php if ($model->canUserControlYou()) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
                [
                    'attribute' => 'tagline',
                ]
            ],
        ]);
    } ?>

    <div class="text-center">
        <?= \yii\helpers\Html::a(
            Yii::t('app', 'BUTTON_SEE_BACKEND'),
            Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl([
                'character/update',
                'id' => $model->character_id
            ]),
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

</div>
