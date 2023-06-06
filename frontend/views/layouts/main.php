<?php

/* @var $this \yii\web\View */

/* @var $content string */

use frontend\assets\AppAsset;
use common\components\FooterHelper;
use common\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
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
        'brandLabel' => 'RPG hub',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default navbar-fixed-top',
        ],
    ]);

    $menuItems = [];

    if (Yii::$app->user->can('operator')) {
        $menuItems[] = ['label' => Yii::t('app', 'BUTTON_GOTO_BACKEND'), 'url' => Yii::$app->params['uri.back']];
    }

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('app', 'MENU_TOP_LOGIN'), 'url' => ['/site/login']];
    } else {
        $menuItems[] = [
            'label' => Yii::t('app', 'MENU_TOP_SETTINGS'),
            'items' => [
                ['label' => Yii::t('app', 'MENU_TOP_SETTINGS'), 'url' => ['/site/settings']],
                ['label' => Yii::t('app', 'MENU_TOP_CHANGE-PASSWORD'), 'url' => ['/site/password-change']]
            ]
        ];

        $epics = \common\models\EpicQuery::activeEpicsAsModels(false);

        $items = [];

        foreach ($epics as $epic) {
            $items[] = '<li>'
                . Html::beginForm(['/site/set-epic'], 'post', ['id' => 'epic-switch-' . $epic->key])
                . Html::input('hidden', 'epic', $epic->key)
                . Html::submitButton($epic->name, ['class' => 'btn btn-link'])
                . Html::endForm()
                . '</li>';
        }

        $epicChoice = [
            'label' => empty(Yii::$app->params['activeEpic'])
                ? Yii::t('app', 'MENU_TOP_CHOOSE_EPIC')
                : Yii::t('app', 'MENU_TOP_CHANGE_EPIC'),
            'items' => $items,
            'options' => [],
        ];

        if (!empty(Yii::$app->params['activeEpic'])) {
            $epicChoice['options']['title'] = Yii::t(
                'app',
                'MENU_TOP_CHANGE_EPIC_TITLE {name}',
                ['name' => Yii::$app->params['activeEpic']->name]
            );
        }

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
