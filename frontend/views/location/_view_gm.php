<?php

use common\models\Location;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Location */
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
                    'label' => Yii::t('app', 'LOCATION_IMPORTANCE'),
                    'value' => $model->getImportanceCategory(),
                ],
            ],
        ]);
    } ?>

    <div class="text-center">
        <?= Html::a(
            Yii::t('app', 'BUTTON_SEE_BACKEND'),
            Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl([
                'location/view',
                'key' => $model->key
            ]),
            ['class' => 'btn btn-default']
        ) ?>
    </div>
</div>
