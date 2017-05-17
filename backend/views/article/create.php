<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = Yii::t('app', 'TITLE_ARTICLE_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
