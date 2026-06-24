<?php

use common\models\Project;
use frontend\assets\ProjectAsset;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

ProjectAsset::register($this);

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'PROJECT_TITLE_INDEX');
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->params['activeEpic']->name,
    'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="project-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="info-box">' . Yii::t('app', 'PROJECTS_NOT_FOUND') . '</p>',
        'itemOptions' => ['class' => 'item'],
        'itemView' => function (Project $model, $key, $index, $widget) {
            return $this->render(
                '_epic_box',
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
