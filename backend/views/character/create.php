<?php

use backend\assets\CharacterAsset;
use yii\helpers\Html;

CharacterAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Character */

$this->title = Yii::t('app', 'TITLE_CHARACTER_CREATE');
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/front', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_CHARACTER_INDEX'),
    'url' => ['character/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
