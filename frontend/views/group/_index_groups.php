<?php

/* @var $model Group */
/* @var $dataProvider yii\data\ActiveDataProvider */

use common\models\Group;
use yii\helpers\Html;
use yii\widgets\ListView;

?>

<div id="groups">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="error-box">' . Yii::t('app', 'GROUPS_NOT_FOUND') . '</p>',
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (Group $model, $key, $index, $widget) {
            return $this->render(
                '_index_box',
                [
                    'model' => $model,
                    'key' => $key,
                    'index' => $index,
                    'widget' => $widget,
                ]
            );
        },
    ]) ?>
</div>
