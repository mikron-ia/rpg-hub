<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CharacterSheet */

$this->title = Yii::t('app', 'CHARACTER_SHEET_TITLE_CREATE');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'CHARACTER_SHEET_TITLE_INDEX'),
    'url' => ['character-sheet/index', 'epic' => $model->epic->key]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-sheet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
