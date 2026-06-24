<?php

use common\models\Article;
use common\models\core\Visibility;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'ARTICLE_TITLE_INDEX'),
    'url' => ['index', 'key' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;

$this->params['showPrivates'] = $showPrivates = $model->canUserControlYou();
?>
<div class="article-view col-lg-10 col-lg-offset-1 col-md-12">

    <div class="buttoned-header">
        <h1>
            <?php if ($model->getVisibility() === Visibility::VISIBILITY_GM): ?>
                <span class="unpublished-tag tag-view-page" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_M') ?>">
                    <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_M') ?>
                </span>
            <?php endif; ?>
            <?php if ($model->getVisibility() === Visibility::VISIBILITY_DESIGNATED): ?>
                <span class="designated-tag tag-view-page" title="<?= Yii::t('app', 'TAG_TITLE_DESIGNATED_M') ?>">
                    <?= Yii::t('app', 'TAG_LABEL_DESIGNATED_M') ?>
                </span>
            <?php endif; ?>
            <?= Html::encode($this->title) ?>
        </h1>
        <?php if ($showPrivates): ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_BACKEND'),
                Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl([
                    'article/view',
                    'key' => $model->key,
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        <?php endif; ?>
    </div>

    <p class="subtitle"><?= $model->subtitle ?></p>

    <?php if (!empty($model->outline_ready)): ?>
        <div class="outline-box">
            <?= $model->getOutlinedFormatted() ?>
        </div>
    <?php endif; ?>

    <div>
        <?= $showPrivates ? $model->getTextFormattedForOperator() : $model->getTextFormattedForUser() ?>
    </div>

    <?php if ($showPrivates && !empty($model->notes_raw)): ?>
        <div class="col-lg-12 secret-text-box">
            <?= $model->getNotesFormatted(); ?>
        </div>
    <?php endif; ?>

</div>
