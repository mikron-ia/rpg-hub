<?php

use common\models\Person;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PersonQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_PEOPLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-index">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
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
            'columns' => [
                [
                    'attribute' => 'name',
                    'value' => function (Person $model) {
                        return StringHelper::truncateWords($model->name, 5, ' (...)', false);
                    }
                ],
                [
                    'attribute' => 'tagline',
                    'value' => function (Person $model) {
                        return StringHelper::truncateWords($model->tagline, 5, ' (...)', false);
                    }
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
