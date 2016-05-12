<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = Yii::t('app', 'STORY_TITLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'STORY_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
