<?php

use common\models\core\Visibility;
use common\models\Location;
use frontend\assets\LocationAsset;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

LocationAsset::register($this);

/* @var $this View */
/* @var $model Location */
/* @var $storyLocationPublic array<string> */
/* @var $storyLocationPrivate array<string> */
/* @var $showPrivates bool */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->epic->name, 'url' => ['epic/view', 'key' => $model->epic->key]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'TITLE_LOCATIONS_INDEX'),
    'url' => ['index', 'key' => $model->epic->key],
];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showPrivates'] = $showPrivates;

$items = [
    [
        'label' => Yii::t('app', 'LOCATION_DESCRIPTIONS_COMPACT_TAB'),
        'content' => $this->render('../_descriptions-compact/_view_descriptions', [
            'model' => $model,
            'showPrivates' => $showPrivates,
        ]),
        'encode' => false,
        'active' => true,
    ],
    [
        'label' => Yii::t('app', 'LOCATION_DESCRIPTIONS_TAB'),
        'content' => $this->render('../_descriptions/_view_descriptions', [
            'model' => $model,
            'showPrivates' => $showPrivates,
        ]),
        'encode' => false,
        'active' => false,
    ],
];

if ($this->params['showPrivates']) {
    $items[] = [
        'label' => Yii::t('app', 'LOCATION_GM_TAB'),
        'content' => $this->render('_view_gm', ['model' => $model]),
        'encode' => false,
        'active' => false,
    ];
}

?>
<div class="person-view">
    <div class="buttoned-header">
        <h1>
            <?php if ($model->getVisibility() !== Visibility::VISIBILITY_FULL): ?>
                <span class="unpublished-tag tag-view-page"><?= Yii::t('app', 'TAG_UNPUBLISHED_F') ?></span>
            <?php endif; ?>
            <?= Html::encode($this->title) ?>
        </h1>
        <?php if ($this->params['showPrivates']): ?>
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

    <p class="subtitle"><?= $model->tagline ?></p>

    <?= Tabs::widget(['items' => $items]) ?>
</div>
