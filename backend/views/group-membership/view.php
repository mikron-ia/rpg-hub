<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GroupMembership */

$this->title = $model->group_membership_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'GROUP_MEMBERSHIP_TITLE_INDEX'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-membership-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'character.name',
            'group.name',
            'visibility',
            'short_text',
            'public_text:ntext',
            'private_text:ntext',
        ],
    ]) ?>

</div>
