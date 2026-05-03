<?php

use common\models\CharacterQuery;
use common\models\Epic;
use frontend\assets\IndexBoxesLocationAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

IndexBoxesLocationAsset::register($this);

/* @var $this View */
/* @var $epic Epic */
/* @var $searchModel CharacterQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_LOCATIONS_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;

$labelForMain = isset(Yii::$app->request->queryParams['LocationQuery'])
    ? Yii::t('app', 'LOCATION_LABEL_SEARCH_RESULTS')
    : Yii::t('app', 'LOCATION_LABEL_ALL');

$mainTab = [
    'label' => $labelForMain,
    'content' => $this->render('_index_locations', ['dataProvider' => $dataProvider]),
    'encode' => false,
    'active' => true,
];

$searchTab = [
    'label' => Yii::t('app', 'LOCATION_LABEL_SEARCH'),
    'content' => $this->render('_search', ['model' => $searchModel]),
    'encode' => false,
    'active' => false,
];

$allTab = [
    'label' => Yii::t('app', 'LOCATION_LABEL_ALL'),
    'url' => ['location/index', 'key' => $epic->key],
];

if (isset(Yii::$app->request->queryParams['LocationQuery'])) {
    $items = [$allTab, $searchTab, $mainTab];
} else {
    $items = [$mainTab, $searchTab];
}

?>

<div class="location-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>
    <?= Tabs::widget(['items' => $items]) ?>
</div>

<?php Modal::begin([
    'id' => 'scribble-modal',
    'header' => '<h2 class="modal-title modal-title-centered">' . Yii::t('app', 'SCRIBBLE_TITLE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>
