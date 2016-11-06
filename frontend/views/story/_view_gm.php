<?php

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
</div>
