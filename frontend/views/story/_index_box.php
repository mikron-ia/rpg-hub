<?php

use yii\helpers\Html;

/** @var $model \common\models\Story */

?>

<div id="story-{{ loop.index }}">

    <h2 class="center">
        <?php echo Html::a(Html::encode($model->name), ['view', 'id' => $model->story_id]); ?>
    </h2>

    <div class="col-lg-8">
        <?php echo $model->short; ?>
    </div>

    <div class="col-lg-4">

        <table class="table table-bordered table-hover">
            <tbody>
            <?php foreach ($model->storyParameters as $storyParameter): ?>
                <tr title="<?php echo "NYI" ?>">
                    <td class="text-left"><?php echo $storyParameter->code; ?></td>
                    <td class="text-center"><?php echo $storyParameter->content; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<div class="clearfix"></div>