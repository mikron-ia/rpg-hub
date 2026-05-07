<?php

use common\models\Image;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var Image $model */

$this->title = Yii::t('app', 'IMAGE_TITLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'IMAGE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
