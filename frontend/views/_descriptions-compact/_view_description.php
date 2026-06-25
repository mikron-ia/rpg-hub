<?php

use common\models\core\Visibility;
use common\models\Description;
use yii\web\View;

/* @var $this View */
/* @var $model Description */
/* @var $showPrivates bool */

$boxClasses = ['col-md-6'];
if ($model->getVisibility() !== Visibility::Full) {
    $boxClasses[] = 'secret unpublished-description';
}
?>

<div class="<?= implode(' ', $boxClasses) ?>">
    <div>
        <h2><?= $model->getTypeName(); ?></h2>
    </div>

    <div class="public-notes">
        <?= $showPrivates ? $model->getPublicFormattedForOperator() : $model->getPublicFormattedForUser(); ?>
    </div>

    <?php if ($model->protected_text): ?>
        <div class="protected-notes comment">
            <?= $showPrivates ? $model->getProtectedFormattedForOperator() : $model->getProtectedFormattedForUser(); ?>
        </div>
    <?php endif; ?>

    <?php if ($showPrivates && $model->private_text): ?>
        <div class="private-notes secret">
            <?= $model->getPrivateFormatted(); ?>
        </div>
    <?php endif; ?>
</div>
