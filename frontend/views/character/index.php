<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_CHARACTER_INDEX');
$this->params['breadcrumbs'][] = $this->title;

$labelForMain = isset(Yii::$app->request->queryParams['CharacterQuery'])
    ? Yii::t('app', 'CHARACTER_LABEL_SEARCH_RESULTS')
    : Yii::t('app', 'CHARACTER_LABEL_ALL');

$mainTab = [
    'label' => $labelForMain,
    'content' => $this->render('_index_people', ['dataProvider' => $dataProvider]),
    'encode' => false,
    'active' => true,
];

$searchTab = [
    'label' => Yii::t('app', 'CHARACTER_LABEL_SEARCH'),
    'content' => $this->render('_search', ['model' => $searchModel]),
    'encode' => false,
    'active' => false,
];

$allTab = [
    'label' => Yii::t('app', 'CHARACTER_LABEL_ALL'),
    'url' => ['character/index'],
];

if(isset(Yii::$app->request->queryParams['CharacterQuery'])) {
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
