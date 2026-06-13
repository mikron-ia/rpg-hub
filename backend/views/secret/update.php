<?php

use common\models\Secret;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Secret */

$this->title = Yii::t('app', 'SECRET_TITLE_UPDATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SECRET_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'key' => $model->key]];
$this->params['breadcrumbs'][] = Yii::t('app', 'SECRET_TITLE_UPDATE');
?>
<div class="secret-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
