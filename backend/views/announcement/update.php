<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Announcement $model */

$this->title = Yii::t('app', 'LABEL_UPDATE') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => $model?->epic->name, 'url' => ['epic/front', 'key' => $model?->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ANNOUNCEMENT_TITLE_INDEX'), 'url' => ['index', 'key' => $model?->epic->key]];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>

<div class="announcement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
