<?php

use common\models\core\Visibility;
use common\models\Group;

/* @var $model Group */
?>

<li class="item object-list-brick">
    <span class="object-list-brick-text"><?= $model ?> </span>

    <span class="text-center seen-tag-common <?= $model->showSightingCSS() ?> seen-tag-line object-list-brick-tags">
        <?= $model->showSightingStatus() ?>
    </span>

    <?php if ($model->getVisibility() !== Visibility::Full): ?>
        <span class="text-center unpublished-tag-line object-list-brick-tags">
            <?= Yii::t('app', 'TAG_LABEL_UNPUBLISHED_F') ?>
        </span>
    <?php endif; ?>
</li>
