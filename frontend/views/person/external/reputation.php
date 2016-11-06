<?php

use common\models\core\Visibility;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $model \common\models\Person */
/** @var $reputations \common\models\external\Reputation[] */
?>

<div class="col-md-6">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_NAME') ?></th>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_BALANCE_RANGE') ?></th>
            <th class="text-center"><?= Yii::t('external', 'REPUTATION_BALANCE') ?></th>
            <th title="<?= Yii::t('external', 'REPUTATION_REPUTATION_DICE_TITLE') ?>" class="text-center">
                <?= Yii::t('external', 'REPUTATION_DICE') ?>
            </th>
            <th title="<?= Yii::t('external', 'REPUTATION_RECOGNITION_DICE_TITLE') ?>" class="text-center">
                <?= Yii::t('external', 'REPUTATION_RECOGNITION_DICE') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reputations as $reputation): ?>
            <tr title="<?= $reputation->description ?>" class="text-center">
                <td class="text-left"><?= $reputation->name ?></td>
                <td>
                    <?php
                    if ($reputation->value['positiveMax'] == $reputation->value['negativeMin']) {
                        echo $reputation->value['negativeMin'];
                    } else {
                        echo $reputation->value['negativeMin'] - $reputation->value['positiveMax'];
                    }
                    ?>
                </td>
                <td><?= $reputation->value['balance'] ?>
                <td><?= $reputation->value['dice'] ?>
                <td><?= $reputation->value['recognitionDice'] ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

