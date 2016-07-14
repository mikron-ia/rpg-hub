<?php

/* @var $this yii\web\View */
/* @var $epic Epic */

use common\models\Epic;
use yii\bootstrap\Html;

$this->title = 'RPG hub - control';
?>
<div class="site-index">

    <div class="text-center">

        <h1><?= $epic->name ?></h1>

        <div class="btn-group btn-group-lg">
            <?= Html::a(
                Yii::t('app', 'BUTTON_DETAILS'),
                ['epic/view', 'id' => $epic->epic_id],
                ['class' => 'btn btn-lg btn-success'])
            ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(Yii::t('app', 'BUTTON_STORIES'), ['story/index'], ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_RECAPS'), ['recap/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>

        <div class="btn-group btn-group-lg">
            <?= Html::a(Yii::t('app', 'BUTTON_PEOPLE'), ['person/index'], ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_CHARACTERS'), ['character/index'],
                ['class' => 'btn btn-lg btn-success']); ?>
            <?= Html::a(Yii::t('app', 'BUTTON_GROUP'), ['group/index'], ['class' => 'btn btn-lg btn-success']); ?>
        </div>

    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-md-6">

                <h2><?= Yii::t('app', 'CONFIGURATION_TITLE_INDEX'); ?></h2>

                <p>
                    <?= Html::a(
                        Yii::t('app', 'BUTTON_EPIC_LIST') . ' &raquo;',
                        ['epic/index'],
                        ['class' => 'btn btn-default btn-block']
                    ); ?>
                </p>

                <p>
                    <?= Html::a(
                        Yii::t('app', 'BUTTON_DESCRIPTION_LIST') . ' &raquo;',
                        ['description/index'],
                        ['class' => 'btn btn-default btn-block']
                    ); ?>
                </p>

                <p>
                    <?= Html::a(
                        Yii::t('app', 'BUTTON_PARAMETER_LIST') . ' &raquo;',
                        ['parameter/index'],
                        ['class' => 'btn btn-default btn-block']
                    ); ?>
                </p>

            </div>

        </div>

    </div>
</div>
