<?php

use frontend\assets\GroupAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;

GroupAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_GROUPS_INDEX');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'GROUPS_NOT_FOUND') . '</p>',
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (\common\models\Group $model, $key, $index, $widget) {
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

<?php Modal::begin([
    'id' => 'scribble-modal',
    'header' => '<h2 class="modal-title modal-title-centered" style="align: center;">' . Yii::t('app', 'SCRIBBLE_TITLE') . '</h2>',
    'clientOptions' => ['backdrop' => 'static'],
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php Modal::end(); ?>
