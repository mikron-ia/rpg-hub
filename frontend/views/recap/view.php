<?php

use common\models\Recap;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;

/** @var View $this */
/** @var Recap $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'RECAP_TITLE_INDEX'),
    'url' => ['index', 'key' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;

$showPrivates = $model->canUserControlYou();

YiiAsset::register($this);
?>
<div class="recap-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-lg-12">
        <p class="recap-box-time-view">
            <?= $model->point_in_time_id ? $model->pointInTime->name : '' ?>
        </p>
        <?= $model->getContentFormatted(); ?>
        <?php if (!empty($model->games)): ?>
            <strong class="text-center"><?= Yii::t('app', 'LABEL_GAMES') ?>:</strong>
            <?= $model->getSessionNamesFormatted() ?>
        <?php endif; ?>
    </div>

    <?php if ($showPrivates && !empty($model->notes)): ?>
        <div class="col-lg-12 secret-text-box">
            <?= $model->getNotesFormatted(); ?>
        </div>
    <?php endif; ?>
</div>
