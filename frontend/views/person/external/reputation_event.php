<?php

use common\models\core\Visibility;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $model \common\models\Person */
/** @var $events \common\models\external\ReputationEvent[] */
?>

<div class="col-md-12">

    <p class="note"><?= Yii::t('external', 'REPUTATION_EVENT_NOTE') ?></p>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_EVENT') ?></th>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_NAME') ?></th>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_CHANGE') ?></th>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_WHEN') ?></th>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_NOTES') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr class="text-center">
                <td class="text-left"><?= $event->event['name'] ?></td>
                <td><?= $event->name ?></td>
                <td><?= $event->value ?>
                <td><?= $event->event['time'] ?>
                <td class="text-left"><?= $event->event['description'] ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

