<?php

/* @var $this yii\web\View */
/* @var $model common\models\Parameter */

$this->title = Yii::t('app', 'PARAMETER_TITLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PARAMETER_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parameter-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
