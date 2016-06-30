<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = 'RPG hub - control';

$epics = \common\models\EpicQuery::activeEpicsAsModels();

$items = [];

foreach ($epics as $epic) {
    $items[] = Html::beginForm(['/site/set-epic'], 'post', ['id' => 'epic-switch-' . $epic->key])
        . Html::input('hidden', 'epic', $epic->key)
        . Html::submitButton($epic->name, ['class' => 'btn btn-link'])
        . Html::endForm();
}

?>
<div class="site-index">

    <?php foreach ($items as $item): ?>
        <p><?= $item; ?></p>
    <?php endforeach; ?>

</div>
