<?php

use common\models\Article;
use common\models\core\Visibility;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'), 'url' => ['index', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['showPrivates'] = $model->canUserControlYou();
?>
<div class="article-view col-lg-10 col-lg-offset-1 col-md-12">

    <div class="buttoned-header">
        <h1>
            <?php if ($model->getVisibility() !== Visibility::VISIBILITY_FULL): ?>
                <span class="unpublished-tag tag-view-page"><?= Yii::t('app', 'TAG_UNPUBLISHED_M') ?></span>
            <?php endif; ?>
            <?= Html::encode($this->title) ?>
        </h1>
        <?php if ($this->params['showPrivates']): ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_BACKEND'),
                Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl(['article/view', 'key' => $model->key]),
                ['class' => 'btn btn-default']
            ) ?>
        <?php endif; ?>
    </div>

    <p class="subtitle"><?= $model->subtitle ?></p>

    <?php if (!empty($model->outline_ready)): ?>
        <div class="outline-box">
            <?= $model->outline_ready ?>
        </div>
    <?php endif; ?>

    <div>
        <?= $model->text_ready ?>
    </div>

</div>
