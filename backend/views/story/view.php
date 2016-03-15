<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->story_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->story_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'key',
            'name',
            'short',
            'long:ntext',
            'data:ntext',
        ],
    ]) ?>

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
