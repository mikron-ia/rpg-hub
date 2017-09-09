<?php

/* @var $this yii\web\View */
/* @var $model common\models\Character */

?>

<div class="col-md-12">

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->importancePack->getImportances(),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            'user.username',
            [
                'attribute' => 'importance',
                'enableSorting' => false,
            ],
        ],
    ]) ?>

</div>
