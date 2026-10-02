<?php

use common\models\PerformedAction;
use common\models\PerformedActionQuery;
use yii\data\ActiveDataProvider;
use yii\db\conditions\SimpleCondition;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $searchModel PerformedActionQuery */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'PERFORMED_ACTIONS_TITLE_INDEX');
$this->params['breadcrumbs'][] = $this->title;

/**
 * This is a little, fun nightmare cobbled together to quickly generate links for the listed objects
 *
 * If this functionality proves useful and optimization will be called for, this method should be reworked and moved to
 * PerformedActionController or PerformedActionQuery, likely with ArrayDataProvider as $dataProvider instead (optimally
 * without breaking sorting/search)
 */
$linkGenerator = function (PerformedAction $model) {
    if (!isset($model->class)) {
        return '';
    }

    $qualifiedName = 'common\models\\' . $model->class;

    if (!in_array($qualifiedName, array_keys(PerformedAction::CLASS_ID_LABELS))) {
        return '';
    }

    try {
        $object = $qualifiedName::find()
            ->where(new SimpleCondition(PerformedAction::CLASS_ID_LABELS[$qualifiedName], '=', $model->object_id))
            ->one();

        return !empty($object) ? Html::a(
            '<span class="glyphicon glyphicon-eye-open"></span>',
            [strtolower($model->class) . '/view', 'key' => $object->key]
        ) : '';
    } catch (Throwable) {
        return '';
    }
}
?>
<div class="performed-action-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-9">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterPosition' => null,
            'columns' => [
                'performed_at:datetime',
                'user.username',
                [
                    'attribute' => 'operation',
                    'value' => function (PerformedAction $model) {
                        return $model->getName();
                    }
                ],
                'class',
                [
                    'attribute' => 'object_id',
                    'enableSorting' => false,
                ],
                [
                    'contentOptions' => ['class' => 'action-cell'],
                    'format' => 'raw',
                    'value' => $linkGenerator,
                ]
            ],
        ]); ?>
    </div>

    <div class="col-md-3" id="filter">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>
