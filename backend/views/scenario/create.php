<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Scenario */

$this->title = Yii::t('app', 'SCENARIO_TITLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SCENARIO_INDEX_TITLE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
