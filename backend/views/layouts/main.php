<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

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

    $menuItems = [
        [
            'label' => Yii::t('app', 'CONFIGURATION_TITLE_INDEX'),
            'items' => [
                ['label' => Yii::t('app', 'BUTTON_EPIC_LIST'), 'url' => ['/epic/index']],
                ['label' => Yii::t('app', 'BUTTON_DESCRIPTION_LIST'), 'url' => ['/description/index']],
                ['label' => Yii::t('app', 'BUTTON_PARAMETER_LIST'), 'url' => ['/parameter/index']],
            ]
        ],
        [
            'label' => Yii::t('app', 'MENU_TOP_SETTINGS'),
            'items' => [
                ['label' => Yii::t('app', 'MENU_TOP_CHANGE-PASSWORD'), 'url' => ['/site/password-change']]
            ]
        ],
    ];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('app', 'MENU_TOP_LOGIN'), 'url' => ['/site/login']];
    } else {
        $epics = \common\models\EpicQuery::activeEpicsAsModels();

        $items = [];

        foreach ($epics as $epic) {
            $items[] = '<li>'
                . Html::beginForm(['/site/set-epic'], 'post', ['id' => 'epic-switch-' . $epic->key])
                . Html::input('hidden', 'epic', $epic->key)
                . Html::submitButton($epic->name, ['class' => 'btn btn-link'])
                . Html::endForm()
                . '</li>';
        }

        $menuItems[] = [
            'label' => empty(Yii::$app->params['activeEpic'])
                ? Yii::t('app', 'MENU_TOP_CHOOSE_EPIC')
                : Yii::t('app', 'MENU_TOP_CHANGE_EPIC') . ' (' . Yii::$app->params['activeEpic']->name . ')',
            'items' => $items,
        ];

        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                Yii::t('app', 'MENU_TOP_LOGOUT') . ' (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link']
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
                'label' =>  Yii::t('app', 'BREADCRUMBS_HOME'),
                'url' => Yii::$app->homeUrl
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Mikron <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
