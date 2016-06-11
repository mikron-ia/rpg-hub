<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Description */

$this->title = Yii::t('app', 'DESCRIPTION_TITLE_CREATE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="description-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
