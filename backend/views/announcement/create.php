<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Announcement $model */

$this->title = Yii::t('app', 'TITLE_ANNOUNCEMENT_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ANNOUNCEMENT_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="announcement-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
