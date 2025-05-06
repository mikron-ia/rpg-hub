<?php

use yii\helpers\Html;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var common\models\Recap $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'RECAP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="recap-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <div>
        <p class="recap-box-time-view">
            <?= $model->point_in_time_id ? $model->pointInTime->name : '' ?>
        </p>
        <?= $model->getContentFormatted(); ?>
        <?php if (!empty($model->games)): ?>
            <strong class="text-center"><?= Yii::t('app', 'LABEL_GAMES') ?>:</strong>
            <?= $model->getSessionNamesFormatted() ?>
        <?php endif; ?>
    </div>
</div>
