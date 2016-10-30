<?php

/* @var $this yii\web\View */
/* @var $epic \common\models\Epic */

use yii\bootstrap\Html;

$this->title = 'RPG hub - index';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?= Yii::t('app', 'FRONTPAGE_TITLE') ?></h1>
        <?php if ($epic): ?>

            <div class="btn-group btn-group-lg">
                <?= Html::a(Yii::t('app', 'BUTTON_STORIES'), ['story/index'], ['class' => 'btn btn-lg btn-success']); ?>
            </div>

            <div class="btn-group btn-group-lg">
                <?= Html::a(Yii::t('app', 'BUTTON_PEOPLE'), ['person/index'], ['class' => 'btn btn-lg btn-success']); ?>
            </div>

        <?php else: ?>
            <p class="error-box"><?= Yii::t('app', 'ERROR_NO_EPIC_ACTIVE_FRONTPAGE_BUTTONS') ?></p>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h2 class="text-center" title="FRONTPAGE_IC_TITLE_TEXT"><?= Yii::t('app', 'FRONTPAGE_IC') ?></h2>
        <h3 title="FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT"><?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?></h3>
        <?php if ($epic): ?>
            <p>[placeholder for Epic data]</p>
        <?php else: ?>
            <p class="error-box"><?= Yii::t('app', 'ERROR_NO_EPIC_ACTIVE_FRONTPAGE_IC') ?></p>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h2 class="text-center" title="FRONTPAGE_OOC_TITLE_TEXT"><?= Yii::t('app', 'FRONTPAGE_OOC') ?></h2>
        <h3 title="FRONTPAGE_SESSIONS_TITLE_TEXT"><?= Yii::t('app', 'FRONTPAGE_SESSIONS') ?></h3>
        <h3 title="FRONTPAGE_NEWS_TITLE_TEXT"><?= Yii::t('app', 'FRONTPAGE_NEWS') ?></h3>
    </div>
</div>
