<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = 'RPG hub - control';

$epics = \common\models\EpicQuery::activeEpicsAsModels(true);
$items = [];

foreach ($epics as $epic) {
    $items[] = Html::beginForm(['/site/set-epic'], 'post', ['id' => 'epic-switch-' . $epic->key])
        . Html::input('hidden', 'epic', $epic->key)
        . Html::submitButton($epic->name, ['class' => 'btn btn-primary btn-block'])
        . Html::endForm();
}

?>

<div class="site-index">

    <div class="jumbotron">

        <h1><?= Yii::t('app', 'BACKEND_FRONT_PAGE_TITLE'); ?></h1>

        <p class="lead text-center"><?= Yii::t('app', 'BACKEND_FRONT_PAGE_MAIN_SELECT_EPIC'); ?></p>

        <div class="site-index">

            <?php foreach ($items as $item): ?>
                <p><?= $item; ?></p>
            <?php endforeach; ?>

        </div>

    </div>

</div>