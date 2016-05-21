<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Epic */

$this->title = Yii::t('app', 'LABEL_EPIC') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_EPICS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="epic-view">

    <h1>
        <?= Html::encode($this->title) ?>
        <span class="pull-right">
                <?= Html::a(
                    Yii::t('app', 'BUTTON_UPDATE'),
                    ['update', 'id' => $model->epic_id],
                    ['class' => 'btn btn-primary']);
                ?>
            </span>
    </h1>

    <p><b><?= $model->getAttributeLabel('key'); ?>:</b> <?= $model->key; ?></p>

    <p><b><?= $model->getAttributeLabel('system'); ?>:</b> <?= $model->system; ?></p>

</div>
