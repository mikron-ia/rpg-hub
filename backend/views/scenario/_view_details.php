<?php

use common\models\Scenario;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Scenario */

?>

<div>
    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'LABEL_BASIC_DATA_AND_OPERATIONS'); ?></h2>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'format' => 'raw',
                    'label' => Yii::t('app', 'LABEL_EPIC'),
                    'value' => Html::a($model->epic->name, ['epic/front', 'key' => $model->epic->key], []),
                ],
                'key',
                'name',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => $model->getStatus(),
                ],
                'tag_line',
            ],
        ]) ?>

        <div class="text-center">
            <?= Html::a(
                Yii::t('app', 'BUTTON_UPDATE'),
                ['update', 'key' => $model->key],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(
                Yii::t('app', 'BUTTON_SEE_FRONTEND'),
                Yii::$app->params['uri.front'] . Yii::$app->urlManager->createUrl([
                    'scenario/view',
                    'key' => $model->key
                ]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>

    <?php if (!empty($model->stories)): ?>
        <div class="col-md-6">
            <h2 class="text-center"><?= Yii::t('app', 'SCENARIO_STORIES'); ?></h2>
            <ul>
                <?php foreach ($model->stories as $story): ?>
                    <li><?= $story ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
