<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */

$this->title = Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_VIEW {name}', ['name' => $model->character->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['activeEpic']->name, 'url' => ['epic/view', 'key' => Yii::$app->params['activeEpic']->key]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-membership-view">

    <p class="pull-right"><?= Html::tag('span', $model->getStatus(), ['class' => ['membership-status', 'pull-left', $model->getStatusClass()]]) ?></p>
    <p class="subtitle"><?= $model->short_text ?></p>

    <div><?= $model->getPublicFormatted(); ?></div>

    <div class="private-notes"><?= $model->getPrivateFormatted(); ?></div>

    <?php $this->registerJs("$('#membership-view-modal-title').html('" . $this->title . "');"); ?>

</div>
