<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Group */

$this->title = Yii::t('app', 'TITLE_GROUPS_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_GROUPS_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
