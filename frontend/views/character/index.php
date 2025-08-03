<?php

use common\dto\CharacterListDataObject;
use frontend\assets\IndexBoxesCharacterAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

IndexBoxesCharacterAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tabsFromGroupData yii\data\ActiveDataProvider[] */

$this->title = Yii::t('app', 'TITLE_CHARACTER_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;

$labelForMain = isset(Yii::$app->request->queryParams['CharacterQuery'])
    ? Yii::t('app', 'CHARACTER_LABEL_SEARCH_RESULTS')
    : Yii::t('app', 'CHARACTER_LABEL_ALL');

$mainTab = [
    'label' => $labelForMain,
    'content' => $this->render('_index_characters', ['dataProvider' => $dataProvider]),
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

$groupTabs = array_map(function (CharacterListDataObject $tabData) {
    return [
        'label' => $tabData->name,
        'content' => $this->render('_index_characters', ['dataProvider' => $tabData->dataProvider]),
        'encode' => false,
        'active' => false,
    ];
}, $tabsFromGroupData);

if (isset(Yii::$app->request->queryParams['CharacterQuery'])) {
    $items = array_merge([$allTab], $groupTabs, [$searchTab, $mainTab]);
} else {
    $items = array_merge([$mainTab], $groupTabs, [$searchTab]);
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
