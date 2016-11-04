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

    <div id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <div id="people">
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'index-box'],
            'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
            'itemView' => function (\common\models\Person $model, $key, $index, $widget) {
                return $this->render(
                    '_index_box',
                    [
                        'model' => $model,
                        'key' => $key,
                        'index' => $index,
                        'widget' => $widget,
                    ]
                );
            },
        ]) ?>
    </div>

</div>
