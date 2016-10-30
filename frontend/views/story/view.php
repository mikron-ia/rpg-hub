<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'STORY_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-9">

    <?php if ($model->canUserControlYou()) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'key',
            ],
        ]);
    } ?>

    <div>
        <h2><?= Yii::t('app', 'STORY_HEADER_SHORT'); ?></h2>
        <?= $model->getShortFormatted(); ?>
    </div>

    <div>
        <h2><?= Yii::t('app', 'STORY_HEADER_LONG'); ?></h2>
        <?= $model->getLongFormatted(); ?>
    </div>

    </div>

    <div class="col-md-3">

        <table class="table table-bordered table-hover">
            <tbody>
            <?php foreach ($model->parameterPack->parameters as $storyParameter): ?>
                <tr title="<?php echo "NYI" ?>">
                    <td class="text-left"><strong><?php echo $storyParameter->getCodeName(); ?></strong></td>
                    <td class="text-center"><?php echo $storyParameter->content; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
