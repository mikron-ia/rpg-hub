<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \common\models\Image $model */

$this->title = Yii::t('app', 'IMAGE_TITLE_UPDATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'IMAGE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'image_id' => $model->image_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'IMAGE_TITLE_UPDATE');
?>
<div class="image-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
