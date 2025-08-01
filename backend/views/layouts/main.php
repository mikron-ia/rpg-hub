<?php

/* @var $this View */

/* @var $content string */

use backend\assets\AppAsset;
use common\components\FooterHelper;
use common\models\Epic;
use common\models\EpicQuery;
use common\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'RPG Hub',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default navbar-fixed-top',
        ],
    ]);

    $auxiliaryItems = [
        ['label' => Yii::t('app', 'BUTTON_EPIC_LIST'), 'url' => ['/epic/index']],
    ];

    if (Yii::$app->user->can('listPerformedActions')) {
        $auxiliaryItems[] = [
            'label' => Yii::t('app', 'BUTTON_PERFORMED_ACTION_LIST'),
            'url' => ['/performed-action/index']
        ];
    }

    if (Yii::$app->user->can('controlUser')) {
        $auxiliaryItems[] = ['label' => Yii::t('app', 'BUTTON_USER_INVITATIONS'), 'url' => ['/user/invitations']];
        $auxiliaryItems[] = ['label' => Yii::t('app', 'BUTTON_USER_LIST'), 'url' => ['/user/index']];
    }

    if (Yii::$app->user->can('manager')) {
        $auxiliaryItems[] = ['label' => Yii::t('app', 'BUTTON_EPIC_MANAGEMENT'), 'url' => ['/epic/manage']];
    }

    $auxiliaryItems[] = ['label' => Yii::t('app', 'LABEL_MARKDOWN_HELP'), 'url' => ['/site/markdown-help']];
    $auxiliaryItems[] = ['label' => Yii::t('app', 'LABEL_ABOUT'), 'url' => ['/site/about']];
    $auxiliaryItems[] = ['label' => Yii::t('app', 'BUTTON_GOTO_FRONTEND'), 'url' => Yii::$app->params['uri.front']];

    $menuItems = [];

    if (!Yii::$app->user->isGuest) {
        $menuItems[] = [
            'label' => Yii::t('app', 'CONFIGURATION_TITLE_INDEX'),
            'items' => $auxiliaryItems,
        ];
    }

    $menuItems[] = [
        'label' => Yii::t('app', 'MENU_TOP_SETTINGS'),
        'items' => [
            ['label' => Yii::t('app', 'MENU_TOP_SETTINGS'), 'url' => ['/site/settings']],
            ['label' => Yii::t('app', 'MENU_TOP_CHANGE-PASSWORD'), 'url' => ['/site/password-change']],
        ]
    ];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('app', 'MENU_TOP_LOGIN'), 'url' => ['/site/login']];
    } else {
        $epicChoice = [
            'label' => empty(Yii::$app->params['activeEpic'])
                ? Yii::t('app', 'MENU_TOP_CHOOSE_EPIC')
                : Yii::t('app', 'MENU_TOP_CHANGE_EPIC'),
            'items' => array_map(function (Epic $epic) {
                return ['label' => $epic->name, 'url' => ['/epic/front', 'key' => $epic->key]];
            }, EpicQuery::activeEpicsAsModels(true)),
            'options' => [],
        ];

        if (!empty(Yii::$app->params['activeEpic'])) {
            $epicChoice['options']['title'] = Yii::t(
                'app',
                'MENU_TOP_CHANGE_EPIC_TITLE {name}',
                ['name' => Yii::$app->params['activeEpic']->name]
            );
        }

        $menuItems[] = $epicChoice;

        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                Yii::t('app', 'MENU_TOP_LOGOUT'),
                [
                    'class' => 'btn btn-link',
                    'title' => Yii::t(
                        'app',
                        'MENU_TOP_LOGOUT_TITLE {name}',
                        ['name' => Yii::$app->user->identity->username]
                    ),
                ]
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => Yii::t('app', 'BREADCRUMBS_HOME'),
                'url' => Yii::$app->homeUrl,
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left"><?= FooterHelper::copyright() ?></p>
        <p class="pull-right"><?= FooterHelper::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
