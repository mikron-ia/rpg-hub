<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'CHARACTER_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_CHARACTER_CREATE'), ['create'], ['class' => 'btn btn-success']); ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_GOTO_FILTER'),
            ['#filter'],
            ['class' => 'btn btn-default hidden-lg hidden-md']
        ) ?>
    </div>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

</div>
