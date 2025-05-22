<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Group */
?>

<div class="col-md-6">

    <?php if ($model->canUserControlYou()) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'visibility',
                    'value' => $model->getVisibilityName(),
                ],
                [
                    'attribute' => 'importance_category',
                    'label' => Yii::t('app', 'GROUP_IMPORTANCE'),
                    'value' => $model->getImportanceCategory(),
                ],
            ],
        ]);
    } ?>

    <div class="text-center">
        <?= \yii\helpers\Html::a(
            Yii::t('app', 'BUTTON_SEE_BACKEND'),
            Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl([
                'group/view',
                'key' => $model->key
            ]),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

</div>
