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

    <p class="text-right">
        <?= Html::a(Yii::t('app', 'BUTTON_UPDATE'), ['update', 'id' => $model->story_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'key',
            'data:ntext',
        ],
    ]) ?>

    <h2>Short description</h2>
    <?php echo $model->short; ?>

    <h2>Long description</h2>
    <?php echo $model->long; ?>

    <h2>Story parameters</h2>
    <table class="table table-bordered">
        <tbody>
        <?php foreach ($model->storyParameters as $storyParameter): ?>
            <tr>
                <td><?php echo $storyParameter->code; ?></td>
                <td><?php echo $storyParameter->content; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
