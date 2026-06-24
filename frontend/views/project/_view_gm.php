<?php

use common\models\Project;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Project */
?>

<div class="col-md-6">

    <h2 class="text-center"><?= Yii::t('app', 'PROJECT_DETAILS_FOR_GM') ?></h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'key',
            ]
        ],
    ]) ?>

    <div class="text-center">
        <?= Html::a(
            Yii::t('app', 'BUTTON_SEE_BACKEND'),
            Yii::$app->params['uri.back'] . Yii::$app->urlManager->createUrl(['project/view', 'key' => $model->key]),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

</div>

<div class="col-md-6">
    <h2 class="text-center"><?= Yii::t('app', 'PROJECT_NOTES') ?></h2>
    <?= $model->getNotesFormatted(); ?>
</div>
