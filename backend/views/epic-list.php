<?php

/* @var $this yii\web\View */

use common\models\Epic;
use common\models\EpicQuery;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = 'RPG hub - control';

$epics = EpicQuery::activeEpicsAsModels(true);
$items = [];

foreach ($epics as $epic) {
    /** @var Epic $epic */
    $items[] = Html::a(
        $epic->name,
        Url::to(['epic/front', 'key' => $epic->key]),
        ['class' => 'btn btn-primary btn-block btn-epic-choice']
    );
}

?>

<div class="site-index">
    <div class="jumbotron">
        <h1><?= Yii::t('app', 'BACKEND_FRONT_PAGE_TITLE'); ?></h1>

        <p class="lead text-center">
            <?php if (empty($items)): ?>
                <?= Yii::t(
                    'app',
                    'BACKEND_FRONT_PAGE_MAIN_EMPTY_EPIC_LIST {link}',
                    ['link' => Url::to(['epic/index'])],
                ); ?>
            <?php else: ?>
                <?= Yii::t('app', 'BACKEND_FRONT_PAGE_MAIN_SELECT_EPIC'); ?>
            <?php endif; ?>
        </p>

        <div class="site-index">
            <?php foreach ($items as $item): ?>
                <?= $item; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
