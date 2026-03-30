<?php

use common\models\Story;
use yii\web\View;

/* @var $this View */
/* @var $model Story */
/* @var $showPrivates bool */
?>

<div class="col-md-8">
    <h2><?= Yii::t('app', 'STORY_SUMMARY') ?></h2>
    <?= $model->getShortFormatted(); ?>
</div>

<div class="col-md-4">
    <table class="table table-bordered table-hover">
        <tbody>
        <?php foreach ($model->parameterPack->parametersOrdered as $storyParameter): ?>
            <tr>
                <td class="text-left"><strong><?php echo $storyParameter->getCodeName(); ?></strong></td>
                <td class="text-center"><?php echo $storyParameter->content; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>