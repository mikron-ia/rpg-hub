<?php

use common\models\Project;
use yii\web\View;

/* @var $this View */
/* @var $model Project */
/* @var $showPrivates bool */
?>

<div class="col-md-8">
    <h2><?= Yii::t('app', 'PROJECT_SUMMARY') ?></h2>
    <?= $model->getShortFormatted(); ?>
</div>

<div class="col-md-4">
    <table class="table table-bordered table-hover">
        <tbody>
        <?php foreach ($model->parameterPack->parametersOrdered as $projectParameter): ?>
            <tr>
                <td class="text-left"><strong><?php echo $projectParameter->getCodeName(); ?></strong></td>
                <td class="text-center"><?php echo $projectParameter->content; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>