<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_index_box',['model' => $model]);
        },
    ]) ?>

</div>
