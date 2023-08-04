<?php

use common\models\Epic;
use common\models\EpicQuery;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $epics Epic[] */

$epicButtons = [];

foreach (EpicQuery::activeEpicsAsModels(false) as $epic) {
    if (!(isset($objectEpic) && $epic->key == $objectEpic->key)) {
        $epicButtons[] = Html::a(
            $epic->name,
            ['epic/view', 'key' => $epic->key],
            ['class' => ['btn', 'btn-primary', 'btn-lg', 'btn-epic-selection']]
        );
    }
}
?>

<?php foreach ($epicButtons as $button): ?>
    <?= $button; ?>
<?php endforeach; ?>
