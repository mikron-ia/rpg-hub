<?php

use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Scribble $model */

?>
<div class="scribble-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'scribble_id',
            'scribble_pack_id',
            'user_id',
            'favorite',
        ],
    ]) ?>

</div>
