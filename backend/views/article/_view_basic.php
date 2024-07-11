<?php

/* @var $this yii\web\View */

/* @var $model common\models\Article */

use yii\helpers\Html;
use yii\widgets\DetailView;

?>

<div class="col-md-6">
    <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_BASICS') ?></h2>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'epic_id',
                'format' => 'raw',
                'value' => $model->epic_id
                    ? (Html::a($model->epic->name, ['epic/view', 'key' => $model->key], []))
                    : Yii::t('app', 'ARTICLE_NO_EPIC'),
            ],
            'key',
            [
                'attribute' => 'visibility',
                'value' => $model->getVisibility()
            ],
            [
                'label' => Yii::t('app', 'ARTICLE_CHARACTER_COUNT'),
                'value' => '?'
            ],
            [
                'label' => Yii::t('app', 'ARTICLE_WORD_COUNT'),
                'value' => '?'
            ],
        ],
    ]) ?>
</div>

<div class="col-md-6">
    <h2 class="text-center"><?= Yii::t('app', 'ARTICLE_OUTLINE_TITLE') ?></h2>
    <?php if (!empty($model->outline_ready)): ?>
        <?= $model->outline_ready ?>
    <?php else: ?>
        <p class="error-box"><?= Yii::t('app', 'ARTICLE_OUTLINE_EMPTY') ?></p>
    <?php endif; ?>

</div>
