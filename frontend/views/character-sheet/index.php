<?php

use common\models\CharacterSheet;
use common\models\CharacterSheetQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/* @var $this View */
/* @var $searchModel CharacterSheetQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX');
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->params['activeEpic']->name,
    'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-sheet-index">
    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="col-md-12">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'emptyText' => '<p class="error-box">' . Yii::t('app', 'CHARACTER_SHEETS_NOT_AVAILABLE') . '</p>',
            'layout' => '{summary}{items}<div class="clearfix"></div>{pager}',
            'itemView' => function (CharacterSheet $model, $key, $index, $widget) {
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
</div>
