<?php

use common\models\Announcement;
use common\models\Epic;
use yii\helpers\Html;
use yii\web\View;

/** @var Epic $epic */
/** @var View $this */
/** @var Announcement $model */

$this->title = Yii::t('app', 'TITLE_ANNOUNCEMENT_CREATE');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'ANNOUNCEMENT_TITLE_INDEX'),
    'url' => ['index', 'key' => $epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="announcement-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
