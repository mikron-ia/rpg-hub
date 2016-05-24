<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GroupQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_GROUPS_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_GROUP_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            'epic.name',
            'name',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
