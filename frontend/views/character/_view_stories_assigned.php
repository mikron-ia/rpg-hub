<?php

use common\dto\LinkWithVisibility;
use common\models\Character;

/* @var $this yii\web\View */
/* @var $header string */
/* @var $model Character */
/* @var $storyCharacterPublic array<LinkWithVisibility> */
/* @var $storyCharacterPrivate array<LinkWithVisibility> */
/* @var $showPrivateWarning bool */

?>

<?php if (!empty($storyCharacterPublic)): ?>
    <div class="col-md-6">
        <?php if ($showPrivateWarning): ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY_PUBLIC'); ?></h2>
        <?php else: ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY'); ?></h2>
        <?php endif; ?>
        <ul>
            <?php foreach ($storyCharacterPublic as $role): ?>
                <li class="<?= $role->isSecret ? 'confidential' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p class="info-box"><?= Yii::t('app', 'STORY_ASSIGNMENT_CHARACTER_EMPTY'); ?></p>
<?php endif; ?>

<?php if (!empty($storyCharacterPrivate)): ?>
    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY_PRIVATE'); ?></h2>
        <ul>
            <?php foreach ($storyCharacterPrivate as $role): ?>
                <li class="<?= $role->isSecret ? 'confidential' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
