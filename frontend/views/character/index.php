<?php

use common\dto\CharacterListDataObject;
use common\models\CharacterQuery;
use common\models\Epic;
use frontend\assets\IndexBoxesCharacterAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

IndexBoxesCharacterAsset::register($this);

/* @var $this View */
/* @var $epic Epic */
/* @var $searchModel CharacterQuery */
/* @var $dataProvider ActiveDataProvider */
/* @var $tabsFromGroupData ActiveDataProvider[] */
/* @var $favorites ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_CHARACTER_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;

$labelForMain = isset(Yii::$app->request->queryParams['CharacterQuery'])
    ? Yii::t('app', 'CHARACTER_TAB_SEARCH_RESULTS')
    : Yii::t('app', 'CHARACTER_TAB_ALL');

$mainTab = [
    'label' => $labelForMain,
    'content' => $this->render('_index_characters', ['dataProvider' => $dataProvider]),
    'encode' => false,
    'active' => true,
];

$searchTab = [
    'label' => Yii::t('app', 'CHARACTER_TAB_SEARCH'),
    'content' => $this->render('_search', ['model' => $searchModel]),
    'encode' => false,
    'active' => false,
];

$allTab = [
    'label' => Yii::t('app', 'CHARACTER_TAB_ALL'),
    'url' => ['character/index', 'key' => $epic->key],
];

$favoriteTab = [
    'label' => Yii::t('app', 'CHARACTER_TAB_FAVORITES'),
    'content' => $this->render('_index_characters', ['dataProvider' => $favorites]),
    'encode' => false,
    'active' => false,
];

$groupTabs = array_map(function (CharacterListDataObject $tabData) {
    return [
        'label' => $tabData->name,
        'content' => $this->render('_index_characters', ['dataProvider' => $tabData->dataProvider]),
        'encode' => false,
        'active' => false,
    ];
}, $tabsFromGroupData);

if (isset(Yii::$app->request->queryParams['CharacterQuery'])) {
    $items = array_merge([$allTab, $favoriteTab], $groupTabs, [$searchTab, $mainTab]);
} else {
    $items = array_merge([$mainTab, $favoriteTab], $groupTabs, [$searchTab]);
}

?>

<div class="person-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Tabs::widget(['items' => $items]) ?>
</div>

<?php Modal::begin([
    'id' => 'scribble-modal',
    'header' => '<h2 class="modal-title modal-title-centered">' . Yii::t('app', 'SCRIBBLE_TITLE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>
