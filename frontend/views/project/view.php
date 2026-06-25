<?php

use common\models\core\Visibility;
use common\models\Project;
use frontend\assets\ProjectAsset;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

ProjectAsset::register($this);

/* @var $this View */
/* @var $model Project */
/* @var $showPrivates bool */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'PROJECT_TITLE_INDEX'),
    'url' => ['index', 'key' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $showPrivates;

$items = [
    [
        'label' => Yii::t('app', 'PROJECT_SHORT_TAB'),
        'content' => $this->render('_view_short', [
            'model' => $model,
            'showPrivates' => $showPrivates,
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'PROJECT_LONG_TAB'),
        'content' => $this->render('_view_long', [
            'model' => $model,
            'showPrivates' => $showPrivates,
        ]),
        'encode' => false,
        'active' => false,
    ],
];

if ($showPrivates) {
    $items[] = [
        'label' => Yii::t('app', 'PROJECT_GM_TAB'),
        'content' => $this->render('_view_gm', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ];
}
?>
<div class="project-view">
    <div class="buttoned-header">
        <h1>
            <?php if ($model->displayCodeName()): ?>
                <span class="type-tag tag-view-page"><?= $model->getCodeName() ?></span>
            <?php endif; ?>
            <?php if ($model->getVisibility() === Visibility::GameMaster): ?>
                <span class="unpublished-tag tag-view-page" title="<?= Yii::t('app', 'TAG_TITLE_UNPUBLISHED_M') ?>">
                    <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_M') ?>
                </span>
            <?php endif; ?>
            <?= Html::encode($this->title) ?>
        </h1>
        <?php if ($showPrivates): ?>
            <?= Html::a(Yii::t('app', 'BUTTON_SECRETS_SHOW'), '#', [
                'class' => 'btn btn-default',
                'onclick' => 'showSecrets()',
                'id' => 'secrets-show',
            ]) ?>
            <?= Html::a(Yii::t('app', 'BUTTON_SECRETS_HIDE'), '#', [
                'class' => 'btn btn-default',
                'onclick' => 'hideSecrets()',
                'id' => 'secrets-hide',
                'style' => 'display: none;'
            ]) ?>
        <?php endif; ?>
    </div>
    <p class="beta-feature-warning" title="<?= Yii::t('app', 'BETA_WARNING_TITLE') ?>">
        <?= Yii::t('app', 'BETA_WARNING_TEXT') ?>
    </p>
    <?= Tabs::widget(['items' => $items]) ?>
</div>
