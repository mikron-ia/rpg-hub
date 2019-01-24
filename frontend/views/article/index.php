<?php

use common\models\Article;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ArticleQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'ARTICLE_TITLE_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <div class="buttoned-header"><h1><?= Html::encode($this->title) ?></h1></div>

    <?php echo \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'ARTICLES_NOT_FOUND') . '</p>',
        'itemOptions' => ['class' => 'item'],
        'summary' => '',
        'itemView' => function ($model, $key, $index, $widget) {
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
