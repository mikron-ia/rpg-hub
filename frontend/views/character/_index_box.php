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

$classesForBox = 'index-box' . ($additionalBoxClasses ? ' ' . $additionalBoxClasses : '');
$titleText = $model->tagline . ($additionalTitleText ? ' ' . $additionalTitleText : '');

?>

<div id="person-<?php echo $model->character_id; ?>" class="<?= $classesForBox ?>" title="<?= $titleText ?>">

    <h3 class="center">
        <?= Html::a(
            Html::encode(StringHelper::truncateWords($model->name, 16, ' (...)', false)),
            ['view', 'id' => $model->character_id]
        ); ?>
    </h3>

    <p class="subtitle">
        <?= StringHelper::truncateWords($model->tagline, 16, ' (...)', false) ?>
    </p>

    <p class="text-center seen-tag-common <?= $model->showSightingCSS() ?> seen-tag-box">
        <?= $model->showSightingStatus() ?>
    </p>

</div>
