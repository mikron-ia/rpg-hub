<?php

use common\models\Recap;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RecapQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'RECAP_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recap-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="text-right">
        <?= Html::a(
            Yii::t('app', 'BUTTON_RECAP_CREATE'),
            ['create'],
            ['class' => 'btn btn-success']);
        ?>
    </p>

    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'key',
                    'format' => 'raw',
                    'value' => function (Recap $model) {
                        return '<span class="key">' . $model->key . '</span>';
                    },
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'time',
                ],
                [
                    'class' => 'yii\grid\ActionColumn'
                ],
            ],
        ]); ?>
    </div>

</div>
