<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Announcement $model */

$this->title = Yii::t('app', 'LABEL_UPDATE') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ANNOUNCEMENT_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'announcement_id' => $model->announcement_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'BREADCRUMBS_UPDATE');
?>

<div class="announcement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
