<?php

use common\models\Recap;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;

/** @var View $this */
/** @var Recap $model */

/* @var bool $showSecretDetails */

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
        <?= $showSecretDetails ? $model->getContentFormattedForOperator() : $model->getContentFormattedForUser(); ?>
    </div>

    <?php if ($showPrivates && !empty($model->notes)): ?>
        <div class="col-lg-8 secret-text-box">
            <?= $model->getNotesFormatted(); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($model->games)): ?>
        <div class="col-lg-3">
            <h2 class="text-center"><?= Yii::t('app', 'LABEL_GAMES'); ?></h2>
            <ul>
                <?php foreach ($model->games as $game): ?>
                    <li><?= $game->basics ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
