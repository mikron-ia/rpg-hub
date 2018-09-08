<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = 'RPG hub - control';

$epics = \common\models\EpicQuery::activeEpicsAsModels(true);
$items = [];

if (isset($objectEpic)) {
    $mainItem = Html::beginForm(['/site/set-epic'], 'post', ['id' => 'epic-switch-' . $objectEpic->key])
        . Html::input('hidden', 'epic', $objectEpic->key)
        . Html::submitButton($objectEpic->name, ['class' => 'btn btn-primary btn-block'])
        . Html::endForm();
} else {
    $mainItem = null;
}

foreach ($epics as $epic) {
    if (!(isset($objectEpic) && $epic->key == $objectEpic->key)) {
        $items[] = Html::beginForm(['/site/set-epic'], 'post', ['id' => 'epic-switch-' . $epic->key])
            . Html::input('hidden', 'epic', $epic->key)
            . Html::submitButton($epic->name, ['class' => 'btn btn-primary btn-block'])
            . Html::endForm();
    }
}

?>

<?php if ($mainItem): ?>
    <h2 class="text-center"><?= Yii::t('app', 'BACKEND_FRONT_PAGE_MAIN_EPIC_SUGGESTED'); ?></h2>
    <p><?= $mainItem; ?></p>
    <h2 class="text-center"><?= Yii::t('app', 'BACKEND_FRONT_PAGE_MAIN_EPIC_LIST'); ?></h2>
<?php endif; ?>

<?php foreach ($items as $item): ?>
    <p><?= $item; ?></p>
<?php endforeach; ?>
