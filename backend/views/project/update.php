<?php

use backend\assets\ProjectAsset;
use common\models\Project;
use yii\helpers\Html;
use yii\web\View;

ProjectAsset::register($this);

/* @var $this View */
/* @var $model Project */

$this->title = Yii::t('app', 'PROJECT_TITLE_UPDATE') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'PROJECT_TITLE_INDEX'),
    'url' => ['project/index', 'epic' => $model->epic->key],
];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>
<div class="project-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
