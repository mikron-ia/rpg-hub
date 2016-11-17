<?php

use common\models\core\Language;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'USER_INDEX_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">


    <div class="buttoned-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'BUTTON_USER_INVITE'), ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'username',
            'email:email',
            [
                'attribute' => 'language',
                'value' => function (User $model) {
                    return (Language::create($model->language))->getName();
                }
            ],
            [
                'attribute' => 'role',
                'label' => Yii::t('app', 'USER_ROLE_NAME'),
                'value' => function (User $model) {
                    return $model->getUserRoleName();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>

</div>
