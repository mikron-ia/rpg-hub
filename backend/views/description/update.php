<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Description */

$this->title = Yii::t('app', 'DESCRIPTION_TITLE_UPDATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'DESCRIPTION_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'LABEL_UPDATE');
?>
<div class="description-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
