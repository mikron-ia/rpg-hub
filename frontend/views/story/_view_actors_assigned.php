<?php

use common\dto\LinkWithVisibility;
use common\models\Group;
use yii\web\View;

/* @var $this View */
/* @var $header string */
/* @var $model Group */
/* @var $storyCharacterPublic array<LinkWithVisibility> */
/* @var $storyCharacterPrivate array<LinkWithVisibility> */
/* @var $storyGroupPublic array<LinkWithVisibility> */
/* @var $storyGroupPrivate array<LinkWithVisibility> */
/* @var $showPrivateWarning bool */

?>
<?php if (empty($storyCharacterPublic) && empty($storyGroupPublic) && empty($storyCharacterPrivate) && empty($storyGroupPrivate)): ?>
    <div class="col-md-12">
        <p class="info-box"><?= Yii::t('app', 'STORY_ASSIGNMENT_CHARACTER_AND_GROUP_EMPTY'); ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($storyCharacterPublic)): ?>
    <div class="col-md-6">
        <?php if ($showPrivateWarning): ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PUBLIC'); ?></h2>
        <?php else: ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_CHARACTER'); ?></h2>
        <?php endif; ?>
        <ul>
            <?php foreach ($storyCharacterPublic as $role): ?>
                <li class="<?= $role->isSecret ? 'confidential redacted' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($storyGroupPublic)): ?>
    <div class="col-md-6">
        <?php if ($showPrivateWarning): ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PUBLIC'); ?></h2>
        <?php else: ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_GROUP'); ?></h2>
        <?php endif; ?>
        <ul>
            <?php foreach ($storyGroupPublic as $role): ?>
                <li class="<?= $role->isSecret ? 'confidential redacted' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


<?php if (!empty($storyCharacterPrivate)): ?>
    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PRIVATE'); ?></h2>
        <ul>
            <?php foreach ($storyCharacterPrivate as $role): ?>
                <li class="<?= $role->isSecret ? 'confidential redacted' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


<?php if (!empty($storyGroupPrivate)): ?>
    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PRIVATE'); ?></h2>
        <ul>
            <?php foreach ($storyGroupPrivate as $role): ?>
                <li class="<?= $role->isSecret ? 'confidential redacted' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
