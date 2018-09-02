<?php

use frontend\assets\GroupAsset;
use yii\helpers\Html;

GroupAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'TITLE_GROUPS_INDEX');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
        'itemView' => function (\common\models\Group $model, $key, $index, $widget) {
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
