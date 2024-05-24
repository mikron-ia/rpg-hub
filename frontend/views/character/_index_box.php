<?php

use common\models\core\Visibility;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $model \common\models\Character */

switch ($model->visibility) {
    case Visibility::VISIBILITY_GM :
        $additionalBoxClasses = 'index-box-gm';
        $additionalTitleText = '('
            . Yii::t('app', 'LABEL_VISIBLE_LOWERCASE_F')
            . ' '
            . $model->getVisibilityLowercase()
            . ')';
        break;
    case Visibility::VISIBILITY_DESIGNATED :
        $additionalBoxClasses = 'index-box-designated';
        $additionalTitleText = '('
            . Yii::t('app', 'LABEL_VISIBLE_LOWERCASE_F')
            . ' '
            . $model->getVisibilityLowercase()
            . ')';
        break;
    default :
        $additionalBoxClasses = '';
        $additionalTitleText = '';
        break;
}

$favorite = false; // to be replaced by an actual value based on the database record

$classesForBox = 'index-box' . ($additionalBoxClasses ? ' ' . $additionalBoxClasses : '');
$favoriteClass = $favorite ? 'glyphicon-tags' : 'glyphicon-tag';
$favoriteTitle = $favorite ? Yii::t('app', 'SCRIBBLES_TITLE_YES') : Yii::t('app', 'SCRIBBLES_TITLE_NO');
$titleText = $model->tagline . ($additionalTitleText ? ' ' . $additionalTitleText : '');

?>

<div id="character-<?php echo $model->key; ?>" class="<?= $classesForBox ?>" title="<?= $titleText ?>">

    <h3 class="index-box-header-narrow">
        <?= Html::a(
            Html::encode(StringHelper::truncateWords(
                $model->name, Yii::$app->params['indexBoxWordTrimming']['title'],
                ' (...)',
                false
            )),
            ['view', 'key' => $model->key]
        ); ?>
    </h3>

    <span class="index-box-header-icon glyphicon <?= $favoriteClass ?> scribble-button"
          data-character-key="<?= $model->key ?>"
          title="<?= $favoriteTitle ?>"
    ></span>

    <p class="subtitle">
        <?= StringHelper::truncateWords(
            $model->tagline,
            Yii::$app->params['indexBoxWordTrimming']['subtitle'],
            ' (...)',
            false
        ) ?>
    </p>

    <p class="text-center seen-tag-common <?= $model->showSightingCSS() ?> seen-tag-box">
        <?= $model->showSightingStatus() ?>
    </p>

</div>
