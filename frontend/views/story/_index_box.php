<?php

use yii\helpers\Html;

/** @var $model \common\models\Story */

?>

<div id="story-<?php echo $model->story_id; ?>">

    <h2 class="center">
        <?php echo Html::a(Html::encode($model->name), ['view', 'id' => $model->story_id]); ?>
    </h2>

    <div class="col-lg-9 text-justify">
        <?php echo $model->short; ?>
    </div>

    <div class="col-lg-3">

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

<div class="clearfix"></div>