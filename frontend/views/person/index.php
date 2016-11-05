<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PersonQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_PEOPLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;

$labelForMain = isset(Yii::$app->request->queryParams['PersonQuery'])
    ? Yii::t('app', 'PEOPLE_LABEL_SEARCH_RESULTS')
    : Yii::t('app', 'PEOPLE_LABEL_ALL');

$mainTab = [
    'label' => $labelForMain,
    'content' => $this->render('_index_people', ['dataProvider' => $dataProvider]),
    'encode' => false,
    'active' => true,
];

$searchTab = [
    'label' => Yii::t('app', 'PEOPLE_LABEL_SEARCH'),
    'content' => $this->render('_search', ['model' => $searchModel]),
    'encode' => false,
    'active' => false,
];

$allTab = [
    'label' => Yii::t('app', 'PEOPLE_LABEL_ALL'),
    'url' => ['person/index'],
];

if(isset(Yii::$app->request->queryParams['PersonQuery'])) {
    $items = [$allTab, $searchTab, $mainTab];
} else {
    $items = [$mainTab, $searchTab];
}

?>
<div class="person-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Tabs::widget([
        'items' => $items
    ]) ?>

</div>