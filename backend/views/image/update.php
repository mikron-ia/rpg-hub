<?php

use common\models\Image;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Image */

$this->title = Yii::t('app', 'IMAGE_TITLE_UPDATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'IMAGE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'IMAGE_TITLE_UPDATE');
?>
<div class="image-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
