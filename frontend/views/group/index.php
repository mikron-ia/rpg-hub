<?php

use frontend\assets\IndexBoxesGroupAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

IndexBoxesGroupAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\CharacterQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_GROUPS_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;

$labelForMain = isset(Yii::$app->request->queryParams['GroupQuery'])
    ? Yii::t('app', 'GROUP_LABEL_SEARCH_RESULTS')
    : Yii::t('app', 'GROUP_LABEL_ALL');

$mainTab = [
    'label' => $labelForMain,
    'content' => $this->render('_index_groups', ['dataProvider' => $dataProvider]),
    'encode' => false,
    'active' => true,
];

$searchTab = [
    'label' => Yii::t('app', 'GROUP_LABEL_SEARCH'),
    'content' => $this->render('_search', ['model' => $searchModel]),
    'encode' => false,
    'active' => false,
];

$allTab = [
    'label' => Yii::t('app', 'GROUP_LABEL_ALL'),
    'url' => ['group/index'],
];

if (isset(Yii::$app->request->queryParams['GroupQuery'])) {
    $items = [$allTab, $searchTab, $mainTab];
} else {
    $items = [$mainTab, $searchTab];
}

?>

<div class="group-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Tabs::widget(['items' => $items]) ?>
</div>

<?php Modal::begin([
    'id' => 'scribble-modal',
    'header' => '<h2 class="modal-title modal-title-centered" style="align: center;">' . Yii::t('app', 'SCRIBBLE_TITLE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>
