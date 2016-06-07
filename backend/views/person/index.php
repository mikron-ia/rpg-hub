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
        <?= Html::a(Yii::t('app', 'PERSON_BUTTON_CREATE'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'BUTTON_GOTO_FILTER'), ['#filter'], ['class' => 'btn btn-default']) ?>
    </div>

    <div class="col-md-6">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'summary' => '',
            'columns' => [
                [
                    'attribute' => 'name',
                    'value' => function (Person $model) {
                        return StringHelper::truncateWords($model->name, 4, ' (...)', false);
                    }
                ],
                [
                    'attribute' => 'tagline',
                    'value' => function (Person $model) {
                        return StringHelper::truncateWords($model->tagline, 5, ' (...)', false);
                    }
                ],
                [
                    'attribute' => 'visibility',
                    'value' => function (Person $model) {
                        return $model->getVisibilityName();
                    }
                ],
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>

    <div class="col-md-6" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>
