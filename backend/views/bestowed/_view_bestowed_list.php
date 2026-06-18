<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ArrayDataProvider */
?>

<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '<span class="assignment-summary">' . Yii::t('app', 'ASSIGNMENT_SUMMARY {totalCount}') . '</span>',
        'columns' => [
            'username',
        ],
    ]); ?>
</div>
