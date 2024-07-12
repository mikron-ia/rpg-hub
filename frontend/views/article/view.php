<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'), 'url' => ['index', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['showPrivates'] = $model->canUserControlYou();
?>
<div class="article-view">

    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($this->params['showPrivates']): ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_BACKEND'),
                Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl(['article/view', 'key' => $model->key]),
                ['class' => 'btn btn-default']
            ) ?>
        <?php endif; ?>
    </div>

    <p class="subtitle"><?= $model->subtitle ?></p>

    <div class="col-md-12">
        <?= $model->text_ready ?>
    </div>

</div>
