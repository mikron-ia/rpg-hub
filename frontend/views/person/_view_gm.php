<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
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
</div>
