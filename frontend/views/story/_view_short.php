<?php
/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $showPrivates bool */
?>

<div class="col-md-8">
    <?= $model->getShortFormatted(); ?>
</div>

<div class="col-md-4">

    <table class="table table-bordered table-hover">
        <tbody>
        <?php foreach ($model->parameterPack->parameters as $storyParameter): ?>
            <tr title="<?= Yii::t('app', 'PLACEHOLDER_NOT_YET_IMPLEMENTED') ?>">
                <td class="text-left"><strong><?php echo $storyParameter->getCodeName(); ?></strong></td>
                <td class="text-center"><?php echo $storyParameter->content; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>